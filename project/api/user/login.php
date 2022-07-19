<?
    /* 用户登录API */
    
    list($uid, $pvr, $json, $file) = app_check('xva', ['name', 'pass']);
    
    $count = sql_query_count('SELECT id FROM user WHERE name LIKE ?', [$json['name']]);
    $data = sql_query1('SELECT * FROM user WHERE name LIKE ? AND BINARY pass LIKE ?', [$json['name'], $json['pass']]);
    if (! $count) {
        api_callback(0, '用户不存在哦~');
    } elseif (! $data) {
        api_callback(0, '密码错了呢~');
    }
    
    $token = app_genUserToken(intval($data['id']));
    if (! $token) {
        api_callback(0, '数据操作失败了~');
    }
    
    api_callback(1, '', ['userToken' => $token]);
?>