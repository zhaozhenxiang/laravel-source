<?php
/**
 * @power 给定参数是否是PIC
 */
if (!function_exists('isPic')) {
    function isPic($path)
    {
        if (empty($path)) {
            return FALSE;
        }

        $array = pathinfo($_SERVER['REQUEST_URI']);
        if (empty($array['extension'])) {
            return FALSE;
        }

        return preg_match('/png|jpg|gif|jpeg/', $array['extension']);
    }
}

/**
 * @power 当前url是否是PIC请求
 */
if (!function_exists('UrlIsPic')) {
    function UrlIsPic()
    {
        if (!isset($_SERVER['REQUEST_URI'])) {
            return FALSE;
        }

        return isPic($_SERVER['REQUEST_URI']);
    }
}

/**
 * @power 获取uuid(不带有{})
 * @return string
 */
if (!function_exists('getUUID')) {
    function getUUID()
    {
        mt_srand((double) microtime() * 10000);
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45);
        $uuid = substr($charid, 0, 8) . $hyphen
            . substr($charid, 8, 4) . $hyphen
            . substr($charid, 12, 4) . $hyphen
            . substr($charid, 16, 4) . $hyphen
            . substr($charid, 20, 12);

        return $uuid;
    }
}

