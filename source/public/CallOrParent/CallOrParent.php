<?php
class ParentC
{
    public function a()
    {
        var_dump(__CLASS__ . __FUNCTION__);
    }

    public static function c()
    {
        var_dump(__CLASS__ . __FUNCTION__);
    }
}

Class ChildC extends ParentC
{
    public function b()
    {
        static::c();
    }

    public function d()
    {
        self::c();
    }
    public function e()
    {
        var_dump(new static);
        var_dump(new self);
    }

    public function __call($method, $param)
    {
        var_dump(__CLASS__ . __FUNCTION__);
    }
}

$instance = new ChildC();
$instance->a();
$instance->b();
$instance->d();
$instance->e();