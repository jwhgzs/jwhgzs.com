<?
    /* 新宁空间-珍贵档案 点赞API */
    
    list($uid, $pvr, $json, $file) = app_check('v');
    
    app_like('PA', $json['id'], $uid);
    
    api_callback(1, '');
?>