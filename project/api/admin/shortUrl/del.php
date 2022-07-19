<?
    /* 后台管理-短链接管理 删除API */
    
    list($uid, $pvr, $json, $file) = app_check('sv', [], 100);
    
    if (! sql_exec_count('DELETE FROM shortUrl WHERE id = ? LIMIT 1', [$json['id']])) {
        api_callback(0, '操作数据库失败了哦~');
    }
    
    api_callback(1, '');
?>