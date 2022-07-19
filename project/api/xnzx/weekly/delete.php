<?
    /* 新宁空间-班级周报 周报删除API */
    
    list($uid, $pvr, $json, $file) = app_check('vas', ['id'], 1);
    
    if (! sql_exec_count('DELETE FROM xnzx_weekly WHERE id = ? LIMIT 1', [intval($json['id'])])) {
        api_callback(0, '操作数据失败！~');
    }
    if (! sql_exec_count('DELETE FROM xnzx_weekly_article WHERE parentId = ?', [intval($json['id'])])) {
        api_callback(0, '操作数据失败！~');
    }
    
    api_callback(1, '');
?>