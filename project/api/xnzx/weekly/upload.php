<?
    /* 新宁空间-班级周报 周报上传API */
    
    list($uid, $pvr, $json, $file) = app_check();
    if (! $json['isNew']) {
        app_check('a', ['title', 'content']);
        if (! $json['editId']) {
            app_check('a', ['pid', 'typist', 'author']);
        }
    } else {
        app_check('as', ['class_', 'term', 'num', 'people'], 1);
    }
    
    if (! $json['isNew']) {
        // 上传/修改电子稿
        if (! $json['editPid']) {
            $wdata = sql_query('SELECT id, author, typist FROM xnzx_weekly_article WHERE parentId = ?', [intval($json['pid'])]);
            $authors = app_xnzx_getWeeklyPeople($wdata, 0);
            $typists = app_xnzx_getWeeklyPeople($wdata, 1);
            $no = false;
            $count = 0;
            if (! in_array($json['author'], $authors)) {
                $no = true;
            }
            if (! in_array($json['typist'], $typists)) {
                $no = true;
            }
            $no2 = true;
            foreach ($authors as $k => $v) {
                if ($v == $json['author'] && $typists[$k] == $json['typist']) {
                    $no2 = false;
                    $count += 1;
                }
            }
            if ($no || $no2) {
                api_callback(0, '这篇作文没有分配给你打哦！');
            }
            if (sql_query_count('SELECT id FROM xnzx_weekly_article WHERE parentId = ? AND author = ? AND typist = ? AND status = 1', [$json['pid'], $json['author'], $json['typist']]) >= $count) {
                api_callback(0, '这篇作文已经提交了哦，如果要修改请到周报详细页~');
            }
        } else {
            if (sql_query_count('SELECT id FROM xnzx_weekly_article WHERE id = ? AND status = 2', [$json['editId']])) {
                api_callback(0, '这篇作文已经审核通过了哦，无法进行修改~');
            }
        }
        
        function formatRow($row, $isTitle = false) {
            /* 基础格式化（暴力正则替换） */
            $ftable = c::$XNZX_WEEKLY_CONFIG['basicFormatter'];
            $row = preg_replace('/\\s/iu', '', $row);
            foreach ($ftable as $k => $v) {
                $row = str_ireplace($k, $v, $row);
            }
            // 坑死了，类似这种正则匹配、替换一定要加上/u修饰符，以支持utf-8，不然会出现一堆莫名其妙的错误
            // *** 注意！！这里的代码顺序不能乱改，否则会出问题
            if (! $isTitle)
                $row = preg_replace('/[\\.·。]{2,}/u', '……', $row);
            else
                $row = preg_replace('/[\\.·。]+/u', '·', $row);
            $row = preg_replace('/[-_]{2,}/u', '——', $row);
            $row = preg_replace('/‘{2,}/u', '“', $row);
            $row = preg_replace('/’{2,}/u', '”', $row);
            $row = preg_replace('/\'{2,}/u', '"', $row);
            $row = preg_replace('/—+/u', '——', $row);
            $row = preg_replace('/…+/u', '……', $row);
            
            /* 进阶格式化（逐字环境分析，替换单个点号、写反的或写成英文的双引号等） */
            $chars = mb_str_split($row);
            $quot = $squot = $seriesNum = 0;
            foreach ($chars as $k => $v) {
                if (mb_strlen($row) > $k + 1) $next = $chars[$k + 1];
                else $next = null;
                // 非小数用的英文点号替换为句号
                if (intval($v) || $v == '0' || strtolower($v) == 'x') {
                    $seriesNum ++;
                } else {
                    $nextIsNum = (intval($next) || $next == '0' || $next == 'x');
                    if ($v == '.' && (! ($seriesNum && $nextIsNum))) {
                        $chars[$k] = '。';
                    }
                    $seriesNum = 0;
                }
                // 引号成双检测、修正
                $quot_arr = ['“', '”', '"'];
                $squot_arr = ['‘', '’', '\''];
                if (in_array($v, $quot_arr) || in_array($v, $squot_arr)) {
                    if (in_array($v, $quot_arr)) {
                        if ($quot % 2 != 0) {
                            $chars[$k] = '”';
                        } else {
                            $chars[$k] = '“';
                        }
                        $quot ++;
                    }
                    if (in_array($v, $squot_arr)) {
                        if ($squot % 2 != 0) {
                            $chars[$k] = '’';
                        } else {
                            $chars[$k] = '‘';
                        }
                        $squot ++;
                    }
                }
            }
            $row = implode('', $chars);
            
            if ($quot % 2 != 0 || $squot % 2 != 0) {
                return false;
            }
            
            return $row;
        }
        $content = $json['content'];
        $carr = explode(PHP_EOL, $content);
        $fcontent = '';
        foreach ($carr as $k => $v) {
            $row = formatRow($v);
            if (! $row) {
                if ($row === false) {
                    api_callback(0, '你的稿件内容里有标点错误（引号不成对，或者分段错误）哦，请检查并修改再提交~');
                } else {
                    continue;
                }
            }
            $fcontent .= $row;
            if ($k != count($carr) - 1)
                $fcontent .= PHP_EOL;
        }
        $json['title'] = formatRow($json['title'], true);
        $json['tiji'] = formatRow($json['tiji']);
        $json['houji'] = formatRow($json['houji']);
        
        if (! $json['editPid']) {
            // 上传
            // 这里一定要记得LIMIT 1，不然一人多稿实现不了
            if (! sql_exec_count('UPDATE xnzx_weekly_article SET title = ?, tiji = ?, content = ?, houji = ?, oriContent = ?, status = 1, postUid = ?, postTime = ?, postIP = ?, postUA = ? WHERE parentId = ? AND author = ? AND typist = ? AND status = 0 LIMIT 1', [$json['title'], $json['tiji'], $fcontent, $json['houji'], $content, $uid, time_microtime(), app_getUserIP(), app_getUserUA(), intval($json['pid']), intval($json['author']), intval($json['typist'])])) {
                api_callback(0, '操作数据失败~');
            }
        } else {
            // 修改
            if (! sql_exec_count('UPDATE xnzx_weekly_article SET title = ?, tiji = ?, content = ?, houji = ?, oriContent = ? WHERE id = ? AND status = 1', [$json['title'], $json['tiji'], $fcontent, $json['houji'], $content, intval($json['editId'])])) {
                api_callback(0, '操作数据失败，或者数据没有更改哦~');
            }
        }
    } else {
        if (! $json['editPid']) {
            // 发起周报
            $authors = $typists = [];
            foreach ($json['people'] as $k => $v) {
                $authors[] = intval($v[0]);
                $typists[] = intval($v[1]);
            }
            
            // --添加weekly表基本信息
            $newId = sql_newId('xnzx_weekly');
            if (! sql_exec_count('INSERT INTO xnzx_weekly (id, class, term, num, note, nameInvisible, postTime) VALUES (?, ?, ?, ?, ?, ?)', [$newId, $json['class_'], $json['term'], intval($json['num']), $json['note'], intval($json['nameInvisible']), time_microtime()])) {
                api_callback(0, '操作数据失败~~');
            }
            
            // --添加打稿人员安排
            foreach ($authors as $k => $v) {
                $author = $v;
                $typist = $typists[$k];
                if (! sql_exec_count('INSERT INTO xnzx_weekly_article (id, parentId, author, typist) VALUES (?, ?, ?, ?)', [sql_newId('xnzx_weekly_article'), $newId, $author, $typist])) {
                    api_callback(0, '操作数据失败~~');
                }
            }
        } else {
            // 修改周报信息
            $authors = $typists = [];
            foreach ($json['people'] as $k => $v) {
                $authors[] = intval($v[0]);
                $typists[] = intval($v[1]);
            }
            
            sql_exec_count('UPDATE xnzx_weekly SET class = ?, term = ?, num = ?, note = ?, nameInvisible = ? WHERE id = ?', [$json['class_'], $json['term'], intval($json['num']), $json['note'], intval($json['nameInvisible']), $json['editPid']]);
            
            function getSameItemNum($authors = [], $typists = [], $author = 0, $typist = 0) {
                $num = 0;
                foreach ($authors as $k => $v) {
                    if (intval($author) == intval($v) && intval($typist) == intval($typists[$k])) {
                        $num ++;
                    }
                }
                return $num;
            }
            // --删多余的
            $old_table = sql_query('SELECT id, author, typist FROM xnzx_weekly_article WHERE parentId = ?', [$json['editPid']]);
            // 删除根本没有这个作者-打稿人搭配的
            foreach ($old_table as $k => $v) {
                if (! getSameItemNum($authors, $typists, $v['author'], $v['typist'])) {
                    if (! sql_exec_count('DELETE FROM xnzx_weekly_article WHERE id = ?', [$v['id']])) {
                        api_callback(0, '操作数据失败~~');
                    }
                }
            }
            // 删除同作者-打稿人搭配多余的
            foreach ($authors as $k => $v) {
                $author = $v;
                $typist = $typists[$k];
                $nn = sql_query_count('SELECT id FROM xnzx_weekly_article WHERE parentId = ? AND author = ? AND typist = ?', [$json['editPid'], $author, $typist]);
                $rn = getSameItemNum($authors, $typists, $author, $typist);
                $dn = $nn - $rn;
                if ($dn > 0) {
                    $dl = sql_query('SELECT id FROM xnzx_weekly_article WHERE parentId = ? AND author = ? AND typist = ? ORDER BY id DESC LIMIT ' . $dn, [$json['editPid'], $author, $typist]);
                    foreach ($dl as $k2 => $v2) {
                        if (! sql_exec_count('DELETE FROM xnzx_weekly_article WHERE id = ?', [$v2['id']])) {
                            api_callback(0, '操作数据失败~~');
                        }
                    }
                }
            }
            // --加新增的
            foreach ($authors as $k => $v) {
                $author = $v;
                $typist = $typists[$k];
                if (sql_query_count('SELECT id FROM xnzx_weekly_article WHERE parentId = ? AND author = ? AND typist = ?', [$json['editPid'], $author, $typist]) < getSameItemNum($authors, $typists, $author, $typist)) {
                    if (! sql_exec_count('INSERT INTO xnzx_weekly_article (id, parentId, author, typist) VALUES (?, ?, ?, ?)', [sql_newId('xnzx_weekly_article'), $json['editPid'], $author, $typist])) {
                        api_callback(0, '操作数据失败~~');
                    }
                }
            }
        }
    }
    api_callback(1, '');
?>