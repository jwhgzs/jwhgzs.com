<?
    /* 新宁空间-班级周报 公共数据API */
    
    list($uid, $pvr, $json, $file) = app_check('x');
    
    $list = sql_query('SELECT * FROM xnzx_weekly ORDER BY id DESC');
    foreach ($list as $k => $v) {
        $progress = sql_query_count('SELECT id FROM xnzx_weekly_article WHERE parentId = ? AND status >= 1', [$v['id']]);
        $d = sql_query('SELECT id, author, typist FROM xnzx_weekly_article WHERE parentId = ?', [$v['id']]);
        $all = count($d);
        $list[$k]['class_'] = $v['class'];
        $list[$k]['finish_progress'] = intval($progress);
        $list[$k]['full_progress'] = intval($all);
        $list[$k]['finish'] = ($list[$k]['finish_progress'] >= $list[$k]['full_progress']);
        $authors = app_xnzx_getWeeklyPeople($d, 0);
        $typists = app_xnzx_getWeeklyPeople($d, 1);
        $list[$k]['authors'] = app_xnzx_sids2names(c::$XNZX_WEEKLY_CONFIG['year'], $v['class'], $authors);
        $list[$k]['typists'] = app_xnzx_sids2names(c::$XNZX_WEEKLY_CONFIG['year'], $v['class'], $typists);
    }
    $data['adminUids'] = app_getAdminTable(false, 1);
    $data['students'] = app_xnzx_getStudents(c::$XNZX_WEEKLY_CONFIG['year'], 0);
    $data['classes'] = app_xnzx_getStudents(c::$XNZX_WEEKLY_CONFIG['year'], 1);
    
    api_callback(1, '', ['weeklyData' => $data, 'weeklyList' => $list]);
?>