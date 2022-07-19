<?
    /* 用户注册API */
    
    list($uid, $pvr, $json, $file) = app_check('xvpa', ['name', 'pass', 'phone', 'phoneVerify']);
    
    $name_min = c::$USERINF_LENGTH['name_min'];
    $name_max = c::$USERINF_LENGTH['name_max'];
    $pass_min = c::$USERINF_LENGTH['pass_min'];
    $pass_max = c::$USERINF_LENGTH['pass_max'];
    if (! (strlen($json['name']) >= $name_min && strlen($json['name']) <= $name_max)) {
        api_callback(0, '用户名长度必须在' . $name_min . '~' . $name_max . '之间~');
    }
    if (! (strlen($json['pass']) >= $pass_min && strlen($json['pass']) <= $pass_max)) {
        api_callback(0, '密码长度必须在' . $pass_min . '~' . $pass_max . '之间~');
    }
    if (! preg_match('/^[A-Za-z0-9_\x{4e00}-\x{9fa5}]+$/u', $json['name'])) {
        api_callback(0, '用户名只能包含汉字/字母/数字/下划线哦~');
    }
    $samePhoneCount = sql_query_count('SELECT id FROM user WHERE phone = ?', [intval($json['phone'])]);
    if ($samePhoneCount !== 0) {
        api_callback(0, '你的手机号已经注册了九尾狐账号了~');
    }
    $sameNameCount = sql_query_count('SELECT id FROM user WHERE name LIKE ?', [intval($json['name'])]);
    if ($sameNameCount !== 0) {
        api_callback(0, '你的用户名已经有人用了哦~');
    }
    
    $result = sql_exec_count('INSERT INTO user (id, name, pass, phone, signupTime, signupIP, signupUA) VALUES (?, ?, ?, ?, ?, ?, ?)', [sql_newId('user'), $json['name'], $json['pass'], $json['phone'], time_microtime(), app_getUserIP(), app_getUserUA()]);
    if (! $result) {
        api_callback(0, '数据操作失败~');
    }
    app_setPhoneVerifyUsed($pvr, 'signup');
    
    api_callback(1, '');
?>