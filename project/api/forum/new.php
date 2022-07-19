<?
    /* 论坛 话题发布、话题发言API */
    
    list($uid, $pvr, $json, $file) = app_check('v');
    if ($json['pid']) {
        app_check('a', ['content']);
    } elseif ($json['id']) {
        app_check('as', ['title', 'content']);
    } else {
        app_check('a', ['classify', 'title', 'content']);
    }
    if (in_array(intval($json['classify']), c::$FORUM_CONFIG['adminClassifyIds'])) {
        app_check('s', [], 100);
    }
    
    // quill的innerHTML不是规范的原生html+css，需要format；并且其图片（<img />）都是base64格式（图片数据直接在content里），需要上传到cos并替换为cos的url以减小主服务器负担
    list($content, $coverImg) = app_quill_format($json['content']);
    
    if ($json['pid'] && (! $json['id'])) {
        // 话题发言
        sql_exec('INSERT INTO forum (id, type, pid, uid, content, postTime, postIP, postUA) VALUES (?, 2, ?, ?, ?, ?, ?, ?)', [sql_newId('forum'), $json['pid'], $uid, $content, time_microtime(), app_getUserIP(), app_getUserUA()]);
    } elseif ($json['id']) {
        // 话题编辑
        sql_exec('UPDATE forum SET classify = ?, title = ?, content = ?, coverImg = ? WHERE id = ?', [$json['classify'], $json['title'], $content, $coverImg, $json['id']]);
    } else {
        // 话题发布
        sql_exec('INSERT INTO forum (id, type, classify, uid, title, content, coverImg, postTime, postIP, postUA) VALUES (?, 1, ?, ?, ?, ?, ?, ?, ?, ?)', [sql_newId('forum'), $json['classify'], $uid, $json['title'], $content, $coverImg, time_microtime(), app_getUserIP(), app_getUserUA()]);
    }    
    api_callback(1, '');
?>