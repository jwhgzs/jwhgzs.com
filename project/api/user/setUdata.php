<?
    /* 用户数据设置API */
    
    list($uid, $pvr, $json, $file) = app_check('a', ['key']);
    if (! in_array($json['key'], c::$USER_EDITABLESQLKEYS)) {
        api_callback(0, '这项数据不能编辑哦~');
    }
    if ($json['key'] == 'pass') {
        if (! $json['value']) {
            api_callback(0, '密码不能为空哦~');
        }
        $pass_min = c::$USERINF_LENGTH['pass_min'];
        $pass_max = c::$USERINF_LENGTH['pass_max'];
        if (! (strlen($json['value']) >= $pass_min && strlen($json['value']) <= $pass_max)) {
            api_callback(0, '密码长度必须在' . $pass_min . '~' . $pass_max . '之间~');
        }
    }
    
    if ($json['key'] == 'pass' && sql_query_count('SELECT pass FROM user WHERE id = ? AND pass = ?', [$uid, $json['value']])) {
        api_callback(0, '你的密码还是跟原来的一模一样啦~');
    }
    if (! sql_exec_count('UPDATE user SET ' . $json['key'] . ' = ? WHERE id = ?', [$json['value'], $uid])) {
        api_callback(0, '操作数据失败了呢~');
    }
    
    api_callback(1, '');
?>