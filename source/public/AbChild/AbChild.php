<?php

abstract class ParentC
{

}

class AbChild extends ParentC
{

}

$a = new AbChild();
var_dump($a instanceof ParentC);