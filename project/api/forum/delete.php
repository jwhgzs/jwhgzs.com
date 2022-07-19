<?
    /* 论坛 删除API */
    
    list($uid, $pvr, $json, $file) = app_check('vs', [], 100);
    
    $_ids = $__ids = [];
    $__ids = sql_query('SELECT id FROM forum WHERE id = ? OR pid = ?', [intval($json['id']), intval($json['id'])]);
    foreach ($__ids as $v) {
        $_ids[] = $v['id'];
    }
    $ids = implode(',', $_ids);
    if ($ids) {
        if (! sql_exec_count('DELETE FROM forum WHERE id IN (' . $ids . ')')) {
            api_callback(0, '操作数据失败了哦~');
        }
        sql_exec('DELETE FROM action WHERE pid IN (' . $ids . ')');
    }
    
    api_callback(1, '');
?>