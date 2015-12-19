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

