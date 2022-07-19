<?
    /* 新宁空间-珍贵档案 详细页数据API */
    
    list($uid, $pvr, $json, $file) = app_check();
    
    $data = sql_query1('SELECT * FROM xnzx_student_table WHERE id = ?', [intval($json['id'])]);
    $d = app_xnzx_PA_getProperties($data['id'], $uid);
    $data['PA_properties'] = $d[0];
    $data['PA_likes'] = $d[1];
    // sort一下让头像(0.jpg)排最前面
    $names = explode(',', $data['PA_photosName']);
    sort($names);
    $data['PA_photosName'] = ($data['PA_photosName'] . '' ? $names : []);
    
    $pad = [];
    $pad['adminUids'] = app_getAdminTable(false, 100);
    
    api_callback(1, '', ['peopleDetail' => $data, 'PAData' => $pad]);
?>