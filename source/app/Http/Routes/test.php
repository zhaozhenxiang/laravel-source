<?php
//route相关的参数
$route->get('route', function(){
    dd(app()['router']->getRoutes());
    dd(app()['router']->current());
    dd(app()['router']->current()->getCompiled()->getRegex());
    dd(app()['router']->current()->getCompiled()->getVariables());
    $urlVar = [];
    foreach (app()['router']->current()->getCompiled()->getVariables() as $key => $val) {
        $urlVar[$val] = app()['router']->current()->$val;
    }
    dd($urlVar);
    dd(app()['router']->current()->getCompiled()->getPathVariables());
    dd(app()['router']->current()->getUri());
});

//app类相关参数
$route->get('app', function(){
    dd(app());
    dd(app()['router'] === app()['router']);
    dd(app()->make(\App\User::class));
});

//env环境
$route->get('env', function(){
    dd(app()['env']);
});

//拿到config
$route->get('config', function(){
    dd(app()['config']);
});