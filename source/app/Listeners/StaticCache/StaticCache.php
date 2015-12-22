<?php

namespace App\Events\StaticCache;

abstract class StaticCache
{
    //函数的注释代码
    private $doc = '';

    private function getDoc()
    {
        //不为空就返回
        if ('' != $this->doc) {
            return $this->doc;
        }

        //获取当前route实例
        $route = app()['router']->current();

        //获取当前action信息
        /**形如
        array:6 [▼
        "middleware" => "CreateStaticHtmlFile"
        0 => Closure {#107 ?}
        "uses" => Closure {#107 ?}
        "namespace" => "App\Http\Controllers"
        "prefix" => null
        "where" => []
        ]
         */
        $action = $route->getAction();

        if (!is_string($action['uses'])) {
            return '';
        }

        list($class, $method) = explode('@', $action['uses']);

        //反射到class
        //@help http://php.net/manual/en/class.reflectionclass.php
        $classer = new \ReflectionClass($class);
        //获取methods
        //@help http://php.net/manual/zh/class.reflectionmethod.php
        $methoder = $classer->getMethod($method);
        //获取方法的注释
        $this->doc = $methoder->getDocComment();

        return $this->doc;
    }

    /**
     * @return bool
     */
    public function isExpire()
    {
        return TRUE;
    }

    /**
     * @power获取doc的解析之后的数组
     * @return array
     */
    public function docParser()
    {
        $doc = $this->getDoc();
        $value = [];
        foreach ($this->templateVar() as $key => $val) {
            preg_match($val, $doc, $$key);
            if (empty($$key)) {
                $$key = [''];
            }
            array_push($value, ${$key}[0]);
        }

        return array_combine(array_keys($this->templateVar()), $value);
    }

    /**
     * @power 获取变量前缀
     * @return mixed|string
     */
    public function getPrefix()
    {
        if (empty($this->prefix)) {
            $this->prefix = env('STATIC_PREFIX');
        }

        return $this->prefix;
    }

    /**
     * @power 解析模板
     * @todo 下一步需要做到把这个方法扩展成一个class
     * @return array
     */
    public function templateVar()
    {
        return [
            'expire' => '/(?<=\@expire:date=).+?(?=\r+)/',
            'max' => '/(?<=\@max:age=).+?(?=\r+)/'
        ];
    }

    //要求子类实现
    public abstract function handle();
    //要求子类实现
    public abstract function readCached();
    //要求子类实现
    public abstract function write();
    //要求子类实现
    public abstract function delete();
    //要求子类实现
    public abstract function getPath();
    //要求子类实现
    public abstract function isMax();
    //要求子类实现
    public abstract function isExists();
}