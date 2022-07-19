<?
    /* 首页轮播数据同步API */
    
    list($uid, $pvr, $json, $file) = app_check('x');
    
    $carouselList = sql_query('SELECT * FROM forum WHERE classify = ?', [c::$FORUM_CONFIG['carouselClassifyId']]);
    
    api_callback(1, '', ['carouselList' => $carouselList]);
?>