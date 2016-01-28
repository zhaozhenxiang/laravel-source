<?php

interface App
{
    public function a();
}

abstract class Bpp implements App
{
    public abstract function b();
}

class Cpp extends Bpp
{
    public function b()
    {
        var_dump(__CLASS__);
    }

    public function a()
    {
        var_dump(__CLASS__);
    }
}

interface Epp extends App
{
    public function e();
}

class Fpp implements Epp
{
    public function e()
    {

    }

    public function a()
    {

    }
}
