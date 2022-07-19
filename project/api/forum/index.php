<?
    /* 论坛 公共数据API */
    
    list($uid, $pvr, $json, $file) = app_check('x');
    
    if (isset($json['classify'])) {
        $list = sql_query('SELECT * FROM forum WHERE classify = ? AND type = 1 ORDER BY id DESC', [intval($json['classify'])]);
        foreach ($list as $k => $v) {
            $list[$k]['looks'] = app_getLooks('forum', $v['id']);
            $list[$k]['likes'] = app_getLikes('forum', $v['id']);
            $list[$k]['replys'] = sql_query_count('SELECT id FROM forum WHERE pid = ?', [$v['id']]);
            $list[$k]['liked'] = app_isLiked('forum', $v['id'], $uid);
            $list[$k]['udata'] = sql_query1('SELECT ' . implode(', ', c::$USER_PUBLICSQLKEYS) . ' FROM user WHERE id = ? LIMIT 1', [$v['uid']]);
        }
    }
    $data['classifies'] = c::$FORUM_CONFIG['classifies'];
    $data['adminClassifies'] = c::$FORUM_CONFIG['adminClassifyIds'];
    $data['defaultClassify'] = c::$FORUM_CONFIG['defaultClassifyId'];
    
    api_callback(1, '', ['forumData' => $data, 'topicList' => $list]);
?>