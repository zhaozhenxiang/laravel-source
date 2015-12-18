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

$route->get('job', function(){
    $request = app()['request'];
    return app('Illuminate\Contracts\Bus\Dispatcher')->dispatch(new \App\Jobs\StaticCache($request, function($a){dd($a);}));
});

$route->get('delete', function(){
    app()->bind('storage', function(){
        return new Storage();
    });

    dd(app()['storage']->disk('local')->delete('/1'));
});

$route->get('path', function(){
    dd(app()['request']->path());
});

$route->get('env', function(){
    $dir = !empty(env('STATIC_PATH')) ? base_path(env('STATIC_PATH')) : storage_path('app');
    dd($dir);
});

$route->get('/pathinfo', function(){
    $path = '/aa/bb';
    dd(pathinfo($path));
    dd(dirname($path));
    dd(basename($path));
});

$route->get('redis', function(){
    (new Redis1())->set('a', 'ada');
});