<?php
class a
{
    public static $b;
}

$b = new a;
$c = new a;
$b::$b = 9;
var_dump($c::$b);