<?
    /* 后台管理-短链接管理 数据API */
    
    list($uid, $pvr, $json, $file) = app_check('s', [], 100);
    
    $data = sql_query('SELECT * FROM shortUrl ORDER BY id DESC');
    
    api_callback(1, '', ['urlList' => $data]);
?>