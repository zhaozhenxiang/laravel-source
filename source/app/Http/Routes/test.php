<?php
//route��صĲ���
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

//app����ز���
$route->get('app', function(){
    dd(app());
    dd(app()['router'] === app()['router']);
    dd(app()->make(\App\User::class));
});

//env����
$route->get('env', function(){
    dd(app()['env']);
});

//�õ�config
$route->get('config', function(){
    dd(app()['config']);
});