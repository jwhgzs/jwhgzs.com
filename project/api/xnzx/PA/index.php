<?
    /* 新宁空间-珍贵档案 公共数据API */
    
    list($uid, $pvr, $json, $file) = app_check();
    
    $list = sql_query('SELECT * FROM xnzx_student_table WHERE PA_visible = 1 AND year = ? AND class = ?', [$json['year'], $json['class']]);
    foreach ($list as $k => $v) {
        $d = app_xnzx_PA_getProperties($v['id'], $uid);
        
        $list[$k]['sid'] = ($v['type'] == 1 ? 10000000 : $v['sid']);
        $list[$k]['PA_properties'] = $d[0];
        $list[$k]['PA_properties_count'] = count($d[0]);
        $list[$k]['PA_likes'] = $d[1];
        
        // 搜索筛选
        $tag = $json['searchTag'];
        if ($tag) {
            // matchLevel越小，越接近搜索结果
            $ok = false;
            $matchTable = [$v['name'], $v['teacher'], $v['sid']];
            $oriLevel = count($matchTable) - 1 + 1;
            foreach ($d[0] as $k2 => $v2) {
                $matchTable[] = $v2['name'];
                $matchTable[] = $v2['detail'];
            }
            
            foreach ($matchTable as $k2 => $v2) {
                if (stripos($v2 . '', $tag) !== false) {
                    $ok = ($k2 > $oriLevel ? $oriLevel + 1 : $k2) + 1;
                    // 默认取可用得最高matchLevel，记得break！
                    break;
                }
            }
            // 所有来自梗的matchLevel相同
            if (! $ok) unset($list[$k]);
            else $list[$k]['PA_matchLevel'] = $ok;
        }
    }
    if (! $json['searchTag']) {
        // 赞数越多位置越高，相同赞数则梗数多的位置越高
        $list = arr_sort($list, 'PA_likes', true, 'sid', false);
    } else {
        $list = arr_sort($list, 'PA_matchLevel', false, 'PA_likes', true);
    }
    
    $pad = [];
    $pad['adminUids'] = app_getAdminTable(false, 100);
    $pad['defaultPhotoVersion'] = c::$XNZX_PA_CONFIG['defaultPhotoVersion'];
    
    api_callback(1, '', ['peopleList' => $list, 'PAData' => $pad, 'searchTag' => $json['searchTag']]);
?>