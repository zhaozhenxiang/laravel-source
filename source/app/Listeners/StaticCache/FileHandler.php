<?php

namespace App\Events\StaticCache;


class FileHandler extends \App\Events\StaticCache\StaticCache
{
    /**
     * @power 处理
     */
    public function handle()
    {

    }

    /**
     * @power 从cached中读取
     */
    public function readCached()
    {

    }

    /**
     * @power 写入
     */
    public function write()
    {

    }

    /**
     * @power 删除
     */
    public function delete()
    {

    }

    //要求子类实现
    public function getPath()
    {

    }

    /**
     * @power 是否超过一个cached周期
     */
    public function isMax()
    {

    }

    /**
     * @power 是否存在cached信息
     */
    public function isExists()
    {

    }
}