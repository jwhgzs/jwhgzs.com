<?
    /* 新宁空间-班级周报 周报过审/取消过审API */
    
    list($uid, $pvr, $json, $file) = app_check('as', ['id'], 1);
    
    $data = sql_query1('SELECT status FROM xnzx_weekly_article WHERE id = ?', [$json['id']]);
    if (! sql_exec_count('UPDATE xnzx_weekly_article SET status = ? WHERE id = ?', [(intval($data['status']) == 2 ? 1 : 2), intval($json['id'])])) {
        api_callback(0, '操作数据失败！~');
    }
    
    api_callback(1, '');
?>