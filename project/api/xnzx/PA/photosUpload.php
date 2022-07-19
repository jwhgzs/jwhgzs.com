<?
    /* 新宁空间-珍贵档案 照片上传API */
    
    list($uid, $pvr, $json, $file) = app_check('sf');
    
    $oriUrl = $file['tmp_name'];
    $imgUrl = $file['tmp_name'] . '_formatted';
    if (! image_toJpeg($oriUrl, $imgUrl)) {
        api_callback(0, '你上传的图片损坏了呢~（也有可能是格式不标准哦）');
    }
    
    $dir = u('static_user://PA') . '/' . $json['id'];
    if (intval($json['index'])) $name = intval($json['index']);
    // index从0开始
    elseif ($json['mode'] == 0) $name = md5(time_microtime());
    elseif ($json['mode'] == 1) $name = '0';
    $result1 = staticcs_upload($imgUrl, $dir . '/' . $name . '.jpg');
    if ($result1)
        $d = sql_query1('SELECT id, PA_photosName FROM xnzx_student_table WHERE id = ?', [$json['id']]);
        $names = ($d['PA_photosName'] . '' ? explode(',', $d['PA_photosName']) : []);
        $names[] = $name;
        $names = implode(',', $names);
        $result2 = sql_exec_count('UPDATE xnzx_student_table SET PA_photosName = ?, PA_photosVersion = PA_photosVersion + 1 WHERE id = ?', [$names, $json['id']]);
    
    if ($result1 && $result2) {
        unlink($oriUrl);
        unlink($imgUrl);
        api_callback(1, '');
    } else {
        api_callback(0, '操作失败了呢~');
    }
?>