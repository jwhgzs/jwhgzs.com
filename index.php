<?php
    require_once __DIR__ . '/config/index.php';
    require_once __DIR__ . '/require/index.php';
    
    function __parseUrl($dir = '', $host = '') {
        $table = c::$ROUTER;
        $domain = strtolower($host);
        $default = $match1 = $match = null;
        foreach ($table as $k => $v) {
            if (preg_match('/' . str_ireplace('/', '\\/', $k) . '/iu', $domain)) {
                $match1 = $v;
                foreach ($v as $k2 => $v2) {
                    if ($k2 !== 0 && preg_match('/' . str_ireplace('/', '\\/', $k2) . '/iu', $dir)) {
                        $match = $v2;
                        break;
                    } elseif ($k2 === 0) {
                        $default = $v2;
                        break;
                    }
                }
                break;
            }
        }
        $match = '' . ($match ? $match : $default);
        $model = ($match1[1] ? $match1[1] : 'default');
        
        if ($match1 === null)
            return false;
        if ($match === null)
            return ['url' => text_format_dir($dir), 'model' => $model];
        $res = str_ireplace('$', $dir, $match);
        $r = $res;
        $base = __DIR__ . '/project/';
        $base2 = __DIR__ . '/model/' . $model . '/';
        $static = file_exists($base2 . 'static.set');
        $paths = [];
        $paths[] = $base . $r;
        $paths[] = $base . $r . '.php';
        $paths[] = $base . $r . '/index.php';
        
        $ok = false;
        foreach ($paths as $k => $v) {
            if (file_exists($v) && (! is_dir($v))) {
                $ok = $v;
            }
        }
        if (! $ok) {
            $ok = $base2 . '/404.php';
            http_response_code(404);
        }
        $ok = text_format_dir($ok);
        
        return ['url' => $ok, 'model' => $model];
    }
    function __parseDomain($domain = '') {
        foreach (c::$ROUTER as $k => $v) {
            if (preg_match('/' . str_ireplace('/', '\\/', $k) . '/iu', $domain)) {
                return true;
            }
        }
        return false;
    }
    
    $__url = text_format_dir($_GET['__url']);
    $__host = $_SERVER['HTTP_HOST'];
    $__urlData = __parseUrl($__url, $__host);
    $__model_url = __DIR__ . '/model/' . $__urlData['model'];
    include_once $__model_url . '/head.php';
    include_once $__urlData['url'];
    include_once $__model_url . '/tail.php';
?>