<?
    /* 强制设备下线API */
    
    list($uid, $pvr, $json, $file) = app_check();
    
    if (! sql_exec_count('UPDATE userToken SET token = \'\' WHERE id = ? LIMIT 1', [intval($json['id'])])) {
        api_callback(0, '操作数据失败~');
    }
    
    api_callback(1, '');
?>