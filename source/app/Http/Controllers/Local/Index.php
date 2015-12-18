<?php

namespace App\Http\Controllers\Local;


class Index extends \App\Http\Controllers\Local\Controller
{
    /**
     * @expire:date=2015/12/19 10:0:0
     * @max:age=10000
     * @todo 这参数可以很多个。expire表示过期时间多少,max表示过期时间多长
     * @power 显示页面
     * @return string
     */
    public function index()
    {
        return __FILE__ . __LINE__;
    }
}