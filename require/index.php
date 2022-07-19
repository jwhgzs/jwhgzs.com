<?php
    
    function text_url2host($url = '') {
        return strtolower(explode('/', $url)[2]);
    }
    
    /* 公共处理 */
    // 跨域检测，参考：https://www.gxlcms.com/PHPjiqiao-375366.html
    $__origin = $_SERVER['HTTP_ORIGIN'];
    $__origin_domain = text_url2host(isset($__origin) ? $__origin : '');
    if ($__origin_domain && __parseDomain($__origin_domain)) {
        header('Access-Control-Allow-Origin: ' . $__origin);
    }
    // 正则表达式长度限制解除，参考：https://blog.csdn.net/leafgw/article/details/50381298
    ini_set('pcre.backtrack_limit', -1);
    
    /* 函数集 */
    function i($dir = '') {
        global $__model_url;
        require_once $__model_url . '/' . $dir;
    }
    function u($dir = '') {
        $table = c::$URLS_TABLE;
        $tmp = explode('://', $dir);
        $res = $table[$tmp[0]];
        $arr = explode('/', $tmp[1]);
        $result = $res[0];
        
        if (! $res)
            return null;
        
        foreach ($arr as $v) {
            $res = $res[$v];
            if (is_array($res) && $res[0]) {
                $result .= $res[0];
            } elseif ($res && (! is_array($res))) {
                $result .= $res;
            } elseif (! $res) {
                $result .= '/' . $v;
            }
        }
        
        return $result;
    }
    
    function text_hide_phone($phone = '') {
        // 3 + 4 + 4 = 11位
        return substr($phone, 0, 3) . '****' . substr($phone, -4);
    }
    function text_hide_IP($ip = '') {
        $arr = explode('.', $ip);
        return $arr[0] . '.' . $arr[2] . '.*.*';
    }
    function text_format_dir($dir = '') {
        if (! $dir) return '/';
        return preg_replace('/\\/+/iu', '/', $dir);
    }
    function text_random($dig = 16) {
        return substr(md5(md5('' . (time_microtime() + rand())) . 'jwhNb'), 0, $dig);
    }
    function time_microtime() {
        // 这个命名错了哈（应为 毫秒），但是已经用在老多地方了，就懒得改了，有这意思就行
        return intval(microtime(true) * 1000);
    }
    function time_desc($ms = 0) {
        if (! $ms)
            return '';
        return date('Y-m-d H:i:s', $ms / 1000);
    }
    function arr_sortPart($arr = [], $key = '', $isDESC = false) {
        $arr2 = [];
        // 拆成 键=>值 形式
        foreach ($arr as $k => $v) {
            $arr2[$k] = $v[$key];
        }
        // 用 值 来排序，使右边的 值 的顺序对了（注意要保留键值配对）
        if (! $isDESC)
            asort($arr2);
        else
            arsort($arr2);
        
        // 读出正确的 键 的顺序
        $res = [];
        foreach ($arr2 as $k => $v) {
            $res[] = $k;
        }
        
        return $res;
    }
    function arr_sort($arr = [], $key = '', $isDESC = false, $key2 = '', $isDESC2 = false) {
        // 妈的，这个东东搞了我20分钟（主要是以为有简便方法，于是各种查）
        
        // 按 主键 排序
        $_res = arr_sortPart($arr, $key, $isDESC);
        // 末尾必须加一项（多一次foreach），不然最后一项无法处理
        $_res[] = null;
        
        //  主值 相同的，用 副值 排序
        $res = $tmp = [];
        $sameVal = $arr[0][$key];
        foreach ($_res as $v) {
            // 这里比较必须得强类型比较。不然0 == null，就过了。这个问题坑了老久
            if ($arr[$v][$key] !== $sameVal) {
                $tmp = arr_sortPart($tmp, $key2, $isDESC2);
                foreach ($tmp as $v2) {
                    $res[] = $arr[$v2];
                }
                
                if ($v === null) break;
                $tmp = [];
                $tmp[$v] = $arr[$v];
                $sameVal = $arr[$v][$key];
            } else {
                $tmp[$v] = $arr[$v];
            }
        }
        
        return $res;
    }
    
    function image_toJpeg($from_path = '', $to_path = '') {
        $img = imagecreatefromjpeg($from_path);
        if (! $img)
            $img = imagecreatefrompng($from_path);
        if (! $img)
            $img = imagecreatefromgif($from_path);
        if (! $img)
            return false;
        imagejpeg($img, $to_path);
        return true;
    }
    
    $_sql_pdo = new PDO('mysql:host=' . s::$SQL_CONFIG['host'] . ';dbname=' . s::$SQL_CONFIG['dbname'], s::$SQL_CONFIG['user'], s::$SQL_CONFIG['pass']);
    // 注意加了这个才能在sql执行错误时抛出Exception
    $_sql_pdo -> setAttribute(PDO :: ATTR_ERRMODE, PDO :: ERRMODE_EXCEPTION);
    function sql_exec($sql = '', $param = []) {
        try {
            global $_sql_pdo;
            $psm = $_sql_pdo -> prepare($sql);
            $psm -> execute($param);
            return $psm;
        } catch (Exception $ex) {
            api_callback(0, '操作数据库失败了呢~');
        }
    }
    function sql_exec_count($sql = '', $param = []) {
        return sql_exec($sql, $param) -> rowCount();
    }
    function sql_query($sql = '', $param = []) {
        // 不输出含num => value（只有key => value）形式的结果数组的秘密在这：(PDO :: FETCH_ASSOC)参数。
        return sql_exec($sql, $param) -> fetchAll(PDO :: FETCH_ASSOC);
    }
    function sql_query1($sql = '', $param = []) {
        return sql_exec($sql, $param) -> fetch(PDO :: FETCH_ASSOC);
    }
    function sql_query_count($sql = '', $param = []) {
        // TNND被坑了，columnCount拿的是列数，就是字段名数，不是行数。脑塞了，一开始竟然用columnCount取了行数
        return count(sql_exec($sql, $param) -> fetchAll());
    }
    function sql_newId($table = '', $key = 'id', $firstval = 1) {
        return sql_query1('SELECT MAX(' . $key . ') FROM ' . $table)['MAX(' . $key . ')'] + $firstval;
    }
    function sql_fieldsExcept($table = '', $exceptFields = []) {
        // 查询表的所有字段名，参考：https://www.cnblogs.com/TTonly/p/12132651.html
        $result = sql_query('SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA LIKE ? AND TABLE_NAME LIKE ? ORDER BY ORDINAL_POSITION', [s::$SQL_CONFIG['dbname'], $table]);
        $return = [];
        foreach ($result as $k => $v) {
            $n = $v['COLUMN_NAME'];
            if (! in_array($n, $exceptFields)) {
                $return[] = $n;
            }
        }
        return implode(', ', $return);
    }
    function sql_errInfo($psm = null) {
        return $psm -> errorInfo();
    }
    
    function http($url = '', $data = null, $header = [], $cookie = []) {
        /* 来源：https://www.cnblogs.com/dadiaomengmei/p/11447689.html */
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (! empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        if (! empty($header)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        }
        if (! empty($cookie)) {
            curl_setopt($curl, CURLOPT_COOKIE, $cookie);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        curl_close($curl);
        
        if (json_decode($result, true))
            return json_decode($result, true);
        else
            return $result;
    }
    function http_json($url = '', $dataArray = [], $header = [], $cookie = []) {
        $jsonHeader = [
            'Content-type: application/json; charset=\'utf-8\'',
            'Accept: application/json'
        ];
        return http($url, json_encode($dataArray), array_merge($jsonHeader, $header), $cookie);
    }
    
    function api_input($isGET = false) {
        if (! $isGET)
            return json_decode($_POST['json'], true);
        else
            return $_GET;
    }
    function api_inputFile() {
        return $_FILES['file'];
    }
    function api_callback($status = 1, $msg = '', $data = null) {
        header('Content-type: application/json;');
        exit(json_encode(['status' => $status, 'msg' => $msg, 'data' => $data]));
    }
    
    function app_getUserIP() {
        return $_SERVER['REMOTE_ADDR'];
    }
    function app_getUserUA() {
        return $_SERVER['HTTP_USER_AGENT'];
    }
    function app_genUserToken($uid = 0) {
        $token = text_random();
        if (! sql_exec_count('INSERT INTO userToken (id, userId, token, loginTime, loginIP, loginUA) VALUES (?, ?, ?, ?, ?, ?)', [sql_newId('userToken'), $uid, $token, time_microtime(), app_getUserIP(), app_getUserUA()])) {
            return null;
        }
        return $token;
    }
    function app_verifyUserToken($token = '', $verifyIsActive = true, $verifyIsMe = true) {
        if (! $token) return false;
        if ($verifyIsMe) {
            // loginTime + exp >= time, 故loginTime >= time - exp
            $result = sql_query1('SELECT userId FROM userToken WHERE BINARY token LIKE ? AND loginTime >= ? AND loginIP LIKE ? AND loginUA LIKE ?', [$token, time_microtime() - c::$USERTOKEN_EXPTIME, app_getUserIP(), app_getUserUA()]);
        } else {
            $result = sql_query1('SELECT userId FROM userToken WHERE BINARY token LIKE ? AND loginTime >= ?', [$token, time_microtime() - c::$USERTOKEN_EXPTIME]);
        }
        if ($verifyIsActive && (! app_isTokenActive($token))) {
            return false;
        }
        $uid = intval($result['userId']);
        return ($uid ? $uid : false);
    }
    function app_isUserOnline($uid = 0) {
        $data = sql_query('SELECT onlineTime, onlineIP, onlineUA FROM userToken WHERE userId = ?', [$uid]);
        foreach ($data as $k => $v) {
            if (intval($v['onlineTime']) + c::$USERONLINE_INTERVALTIME >= time_microtime()) {
                return true;
            }
        }
        return false;
    }
    function app_isTokenActive($token = '') {
        $data = sql_query1('SELECT onlineTime, onlineIP, onlineUA FROM userToken WHERE BINARY token LIKE ?', [$token]);
        if (! $data) {
            return false;
        }
        if (intval($data['onlineTime']) + c::$USERONLINE_INTERVALTIME < time_microtime()) {
            return false;
        }
        return true;
    }
    function app_getAdminTable($orgin = false, $minLevel = 1) {
        $table = c::$ADMIN_UIDS;
        if ($orgin)
            return $table;
        $res = [];
        foreach ($table as $k => $v) {
            if ($k < $minLevel) continue;
            foreach ($v as $k2 => $v2) {
                $res[] = $v2;
            }
        }
        return $res;
    }
    function app_getAdminLevel($uid = 0) {
        $table = app_getAdminTable(true);
        foreach ($table as $k => $v) {
            if (in_array($uid, $v))
                return $k;
        }
        return false;
    }
    function app_verifyPhone($phone = '', $verifyCode = '') {
        // sendTime + exp >= time, 故sendTime >= time - exp
        $data = sql_query1('SELECT id FROM phoneVerify WHERE phone = ? AND verifyCode = ? AND sendTime >= ? AND used IS NULL AND userIP LIKE ? AND userUA LIKE ?', [intval($phone), intval($verifyCode), time_microtime() - c::$VAPTCHA_SMS_CONFIG['verifyCodeExpTime'], app_getUserIP(), app_getUserUA()]);
        return ($data ? intval($data['id']) : false);
    }
    function app_setPhoneVerifyUsed($id = 0, $intent = 'default') {
        // 顺便把之前未使用的废掉
        $a = sql_exec_count('UPDATE phoneVerify SET used = ? WHERE id = ? AND used IS NULL', [$intent, $id]);
        $b = sql_exec_count('UPDATE phoneVerify SET used = \'expire\' WHERE id = ? AND used IS NULL', [$id]);
        return ($a && $b);
    }
    function app_check($config_text = 'd', $fields = [], $minAdminLevel = 1, $isGET = false) {
        $config = str_split($config_text);
        $json = api_input($isGET);
        
        // x标记的逻辑是，不强制登录，但是登录了的还是取登录信息
        if ((! $json['userToken']) && (! in_array('x', $config))) {
            api_callback(0, '你还没登录呢~');
        } elseif (! $json['userToken']) { } else {
            $uid = app_verifyUserToken($json['userToken'], false);
            if (! $uid) {
                api_callback(0, '用户登录失效，请重新登录哦~');
            }
        }
        if (in_array('s', $config)) {
            if (app_getAdminLevel($uid) < $minAdminLevel) {
                api_callback(0, '你没有管理员权限哦~');
            }
        }
        
        if (in_array('v', $config)) {
            if ($isGET)
                $json['vaptchaData'] = json_decode($json['vaptchaData'], true);
            if (! vaptcha_verify($json['vaptchaData'])) {
                api_callback(0, '人机验证不通过哦~');
            }
        }
        if (in_array('a', $config)) {
            foreach ($fields as $v) {
                if (! isset($json[$v]) || $json[$v] === '') {
                    api_callback(0, '没有填完信息呢~');
                }
            }
        }
        if (in_array('p', $config)) {
            $pvr = app_verifyPhone($json['phone'], $json['phoneVerify']);
            if ($pvr === false) {
                if (strlen($json['phoneVerify']) != 6) {
                    api_callback(0, '手机验证码是6位的，你看错了吧~');
                }
                api_callback(0, '手机验证码错误或失效~');
            }
        }
        $file = api_inputFile();
        if (in_array('f', $config)) {
            if (! $file) {
                api_callback(0, '你的文件呢？');
            }
        }
        
        return [$uid, $pvr, $json, $file];
    }
    
    function app_quill_format($oriContent = '') {
        $content = $oriContent;
        
        /* quill的class、样式格式化 */
        $replace_table = c::$QUILL_CONFIG['formatReplaceTable'];
        foreach ($replace_table as $k => $v) {
            $content = str_ireplace($k, $v, $content);
        }
        
        $firstImg = '';
        /* img标签格式化 */
        // 先取出所有的img标签
        preg_match_all('/<img.*?src=["\'](.*?)["\'].*?>/iu', $content, $matchImg);
        // 再一个一个看是否是base64类型。判base64图片正则参考：https://www.cnblogs.com/xianhuiwang/p/7500875.html
        foreach ($matchImg[1] as $v) {
            $ori_v = $v;
            $v = preg_replace('/\s/iu', '', $v);
            $isBase64 = preg_match('/data:.*;base64,(.*)/iu', $v, $matchSrc);
            if ($isBase64) {
                $base64 = $matchSrc[1];
                $tmpName = text_random();
                $imgTmpUrl = sys_get_temp_dir() . '/' . $tmpName;
                $imgToUrl = u('static_user://forum') . '/' . $tmpName . '.jpg';
                $imgContent = base64_decode($base64);
                $img = imagecreatefromstring($imgContent);
                imagejpeg($img, $imgTmpUrl);
                $result1 = staticcs_upload($imgTmpUrl, $imgToUrl);
                if (! $result1) {
                    api_callback(0, '上传图片失败！~');
                }
                unlink($imgTmpUrl);
                $realUrl = u('static') . '/' . $imgToUrl;
                $content = str_ireplace($ori_v, $realUrl, $content);
                if (! $firstImg) $firstImg = $realUrl;
            }
        }
        return [$content, $firstImg];
    }
    
    function app_xnzx_sid2name($year = 0, $class = 0, $sid = 0) {
        $data = sql_query1('SELECT name, uid FROM xnzx_student_table WHERE year = ? AND class = ? AND type = 0 AND sid = ?', [$year, $class, $sid]);
        return [$sid, $data['name'], $data['uid']];
    }
    function app_xnzx_sids2names($year = 0, $class = 0, $sids = []) {
        $result = [];
        foreach ($sids as $k => $v) {
            $result[] = app_xnzx_sid2name($year, $class, $v);
        }
        return $result;
    }
    function app_xnzx_getStudents($year = 0, $getClasses = 0) {
        $students = $classes = [];
        $_classes = sql_query('SELECT DISTINCT class FROM xnzx_student_table WHERE type = 0 AND year = ?', [$year]);
        foreach ($_classes as $k => $v) {
            $_students = sql_query('SELECT sid, name FROM xnzx_student_table WHERE year = ? AND class = ? AND type = 0 AND disabled = 0', [$year, $v['class']]);
            foreach ($_students as $k2 => $v2) {
                $_students[$k2][0] = $v2['sid'];
                $_students[$k2][1] = $v2['name'];
                unset($_students[$k2]['sid']);
                unset($_students[$k2]['name']);
            }
            $students[$v['class']] = $_students;
            $classes[] = $v['class'];
        }
        return ($getClasses ? $classes : $students);
    }
    function app_xnzx_getWeeklyPeople($d = [], $isGetTypists = 0) {
        $authors = $typists = [];
        foreach ($d as $k2 => $v2) {
            $authors[] = $v2['author'];
            $typists[] = $v2['typist'];
        }
        return ($isGetTypists ? $typists : $authors);
    }
    
    function app_xnzx_PA_getProperties($pid = 0, $uid = 0) {
        $likes = 0;
        $list = sql_query('SELECT * FROM xnzx_student_PA WHERE pid = ? ORDER BY type', [$pid]);
        foreach ($list as $k => $v) {
            $list[$k]['detail'] = explode(PHP_EOL, $v['detail']);
            $like = app_getLikes('PA', $v['id']);
            $list[$k]['likes'] = $like;
            $list[$k]['liked'] = app_isLiked('PA', $v['id'], $uid);
            $likes += $like;
        }
        $list = arr_sort($list, 'type', false, 'likes', true);
        return [$list, $likes];
    }
    
    function app_like($parent = '', $id = 0, $uid = 0) {
        if (! sql_query_count('SELECT id FROM action WHERE parent LIKE ? AND pid = ? AND uid = ? AND type = 2', [$parent, intval($id), $uid])) {
            if (! sql_exec_count('INSERT INTO action (id, parent, type, value, pid, uid, actionTime, userIP, userUA) VALUES (?, ?, 2, 1, ?, ?, ?, ?, ?)', [sql_newId('action'), $parent, intval($id), $uid, time_microtime(), app_getUserIP(), app_getUserUA()])) {
                api_callback(0, '操作数据失败了哦~');
            }
        } else {
            if (! sql_exec_count('UPDATE action SET value = ABS(value - 1), actionTime = ?, userIP = ?, userUA = ? WHERE parent LIKE ? AND pid = ? AND uid = ? AND type = 2', [time_microtime(), app_getUserIP(), app_getUserUA(), $parent, intval($id), $uid])) {
                api_callback(0, '操作数据失败了哦~');
            }
        }
    }
    function app_look($parent = '', $id = 0, $uid = 0) {
        if (! sql_query_count('SELECT id FROM action WHERE parent LIKE ? AND pid = ? AND uid = ? AND type = 1 LIMIT 1', [$parent, $id, $uid])) {
            sql_exec('INSERT INTO action (id, parent, type, pid, uid, actionTime, userIP, userUA) VALUES (?, ?, 1, ?, ?, ?, ?, ?)', [sql_newId('action'), $parent, $id, $uid, time_microtime(), app_getUserIP(), app_getUserUA()]);
        }
    }
    function app_getLooks($parent = '', $id = 0) {
        return sql_query_count('SELECT id FROM action WHERE parent LIKE ? AND pid = ? AND type = 1', [$parent, $id]);
    }
    function app_getLikes($parent = '', $id = 0) {
        return sql_query_count('SELECT id FROM action WHERE parent LIKE ? AND pid = ? AND type = 2 AND value = 1', [$parent, $id]);
    }
    function app_isLiked($parent = '', $id = 0, $uid = 0) {
        return sql_query_count('SELECT id FROM action WHERE parent LIKE ? AND pid = ? AND uid = ? AND type = 2 AND value = 1', [$parent, $id, $uid]);
    }
    
    function vaptcha_verify($data = []) {
        $result = http_json($data['server'], [
            'id' => s::$VAPTCHA_CONFIG['vid'],
            'secretkey' => s::$VAPTCHA_CONFIG['key'],
            'scene' => intval($data['scene']),
            'token' => $data['token'],
            'ip' => app_getUserIP()
        ]);
        return $result['success'];
    }
    
    function vaptcha_sms_send($phone = '', $templateIndex = 'default', $templateParam = [], $vaptchaData = []) {
        $result = http_json(u('vaptcha://api/sms'), [
            'smsid' => s::$VAPTCHA_SMS_CONFIG['smsid'],
            'smskey' => s::$VAPTCHA_SMS_CONFIG['smskey'],
            'token' => $vaptchaData['token'],
            'data' => $templateParam,
            'countrycode' => '86',
            'phone' => $phone,
            'templateid' => s::$VAPTCHA_SMS_CONFIG['templateIds'][$templateIndex]
        ]);
        return (intval($result) === 200);
    }
    function staticcs_upload($from = '', $to = '') {
        return rename($from, c::$STATICCS_CONFIG['root'] . '/' . $to);
    }
    function staticcs_rename($from = '', $to = '') {
        return rename(c::$STATICCS_CONFIG['root'] . '/' . $from, c::$STATICCS_CONFIG['root'] . '/' . $to);
    }
    function staticcs_del($url = '') {
        return unlink(c::$STATICCS_CONFIG['root'] . '/' . $url);
    }
    function staticcs_list($url = '') {
        $res = [];
        $obj = opendir($url);
        while (($v = readdir($obj)) !== false){
            $res[] = ['name' => $v, 'type' => is_dir($url . '/' . $v), 'size' => filesize($url . '/' . $v)];
        }
        return $res;
    }
?>