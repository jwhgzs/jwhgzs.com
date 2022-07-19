<?
    /* 用户数据同步API */
    
    list($uid, $pvr, $json, $file) = app_check();
    if (intval($json['uid']) === $uid) {
        $json['uid'] = null;
    }
    
    $real_uid = ($json['uid'] ? intval($json['uid']) : $uid);
    if (! $json['uid']) {
        $count = sql_exec_count('UPDATE userToken SET onlineTime = ?, onlineIP = ?, onlineUA = ?, rand = ? WHERE BINARY token LIKE ?', [time_microtime(), app_getUserIP(), app_getUserUA(), '' . rand(), $json['userToken']]);
        if ($count !== 1) {
            api_callback(0, '操作数据失败了~');
        }
    }
    $udata = sql_query1('SELECT ' . sql_fieldsExcept('user', ['pass']) . ' FROM user WHERE id = ?', [$real_uid]);
    if (! $udata) {
        api_callback(0, '获取数据失败~');
    }
    
    if (! $json['uid']) {
        $loginDetails = sql_query('SELECT id, token, loginTime, loginIP, onlineTime, onlineIP, onlineUA FROM userToken WHERE userId = ? ORDER BY id DESC LIMIT ' . c::$USERLOGINDETAILS_LIMIT, [$real_uid]);
        foreach ($loginDetails as $k => $v) {
            $time = $loginDetails[$k]['onlineTime'];
            if (time_microtime() - intval($time) <= c::$USERONLINE_INTERVALTIME) {
                $time = '在线';
            } else {
                $time = time_desc($time);
            }
            $loginDetails[$k]['onlineTime'] = $time;
            $loginDetails[$k]['loginTime'] = date('Y-m-d H:i:s', $loginDetails[$k]['loginTime'] / 1000);
            $loginDetails[$k]['tokenActive'] = app_verifyUserToken($v['token'], false, false);
            $loginDetails[$k]['isMe'] = ($v['token'] == $json['userToken']);
            unset($loginDetails[$k]['token']);
            unset($loginDetails[$k]['onlineIP']);
            unset($loginDetails[$k]['onlineUA']);
        }
        $udata['loginDetails'] = $loginDetails;
    }
    
    if ($json['uid']) {
        $udata['phone'] = text_hide_phone($udata['phone']);
        $udata['signupIP'] = text_hide_IP($udata['signupIP']);
        $udata['isOnline'] = app_isUserOnline($udata['id']);
        $lastOnline = sql_query1('SELECT MAX(onlineTime) FROM userToken WHERE userId = ?', [$udata['id']]);
        $udata['lastOnlineTime'] = $lastOnline['MAX(onlineTime)'];
    } else {
        $udata['isMe'] = true;
    }
    
    $udata['adminUids'] = app_getAdminTable(0);
    
    api_callback(1, '', ['userData' => $udata]);
?>