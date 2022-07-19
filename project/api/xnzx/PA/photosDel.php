<?
    /* 新宁空间-珍贵档案 照片删除API */
    
    list($uid, $pvr, $json, $file) = app_check('sv');
    
    $dir = u('static_user://PA') . '/' . $json['id'];
    $result1 = staticcs_del($dir . '/' . $json['name'] . '.jpg');
    $d = sql_query1('SELECT id, PA_photosName FROM xnzx_student_table WHERE id = ?', [$json['id']]);
    $names = ($d['PA_photosName'] ? explode(',', $d['PA_photosName']) : []);
    foreach ($names as $k => $v) {
        if (strtolower($v) == strtolower($json['name'])) {
            unset($names[$k]);
        }
    }
    $names = implode(',', $names);
    $result2 = sql_exec_count('UPDATE xnzx_student_table SET PA_photosName = ?, PA_photosVersion = PA_photosVersion + 1 WHERE id = ?', [$names, $json['id']]);
    
    if ($result1 && $result2) {
        api_callback(1, '');
    } else {
        api_callback(0, '操作失败了呢~');
    }
?>