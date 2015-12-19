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

