<?
    /* 论坛 点赞API */
    
    list($uid, $pvr, $json, $file) = app_check('v');
    
    app_like('forum', $json['id'], $uid);
    
    api_callback(1, '');
?>