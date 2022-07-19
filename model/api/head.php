<?
    function exceptionHandler($ex) {
        api_callback(0, '操作失败！');
    }
    set_exception_handler('exceptionHandler');
?>