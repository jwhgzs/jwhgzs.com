<?
    /* 用户头像上传API */
    
    list($uid, $pvr, $json, $file) = app_check('vf');
    
    $oriUrl = $file['tmp_name'];
    $imgUrl = $file['tmp_name'] . '_formatted';
    if (! image_toJpeg($oriUrl, $imgUrl)) {
        api_callback(0, '你上传的图片损坏了呢~（也有可能是格式不标准哦）');
    }
    
    $result1 = staticcs_upload($imgUrl, u('static_user://avatar') . '/' . $uid . '.jpg');
    if ($result1)
        $result2 = sql_exec_count('UPDATE user SET avatarVersion = avatarVersion + 1 WHERE id = ?', [$uid]);
    
    if ($result1 && $result2) {
        unlink($oriUrl);
        unlink($imgUrl);
        api_callback(1, '');
    } else {
        api_callback(0, '操作失败了呢~' . u('static_user://user/avatar') . '/' . $uid . '.jpg');
    }
?>