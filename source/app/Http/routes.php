<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

$route = app()['router'];
require_once app_path('Http/Routes/local.php');
require_once app_path('Http/Routes/product.php');
require_once app_path('Http/Routes/test.php');

$route->get('/', ['middleware' => 'CreateStaticHtmlFile', function () {
    return view('welcome');
}]);
$route->get('/get', ['middleware' => 'CreateStaticHtmlFile', 'uses' => 'Local\Index@index']);
$route->post('/post', ['middleware' => 'CreateStaticHtmlFile', 'uses' => 'Local\Index@index']);
//$route->get('/', function () {
//    return view('welcome');
//});
//$route->get('/{id}', ['middleware' => 'CreateStaticHtmlFile', function ($id) {
//    return view('welcome');
//}]);

$route->get('model', function(){
    $a = DB::table('cached')->toSql();
    $b = \App\Cached::where('adad', 1)->orWhere('asda', 1)->toSql();
    dd($b);
});

$route->get('orm', function(){
//
//    $orm = new \App\Cached();
//
//    dd($orm->first()->getID());

//    dd(\App\Cached::saved());
    dd(\App\Cached::ByID(2)->first());
    //save这种调用是不可以的
    $orm = \App\Cached::ByID(2)->first();
    $orm->id = 3;
    dd($orm->save());
});

$route->get('app', function(){
    $app = app();
    dd($app->bindings);
    dd($app->instances);
});
