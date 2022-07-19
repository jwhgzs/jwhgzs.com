<?
    /* 用户登录（手机号）API */
    
    list($uid, $pvr, $json, $file) = app_check('xvpa', ['phone', 'phoneVerify']);
    
    $data = sql_query1('SELECT * FROM user WHERE phone = ?', [intval($json['phone'])]);
    if (! $data) {
        api_callback(0, '你的手机号未绑定九尾狐账号，请先注册哦！');
    }
    $token = app_genUserToken($data['id']);
    if (! $token) {
        api_callback(0, '操作数据失败了~');
    }
    app_setPhoneVerifyUsed($pvr, 'phoneLogin');
    
    api_callback(1, '', ['userToken' => $token]);
?>