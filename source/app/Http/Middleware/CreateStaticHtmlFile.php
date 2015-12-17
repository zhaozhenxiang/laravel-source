<?php

/**
 * @idea 现在静态文件类型的cached是很少数的，该页面只适合浏览性页面
 * @idea 我们认为路由中没有参数的路由应该不含有DB操作（该响应不会从DB中读取数据写入到view中）
 * @idea 我们认为路由中有参数的路由应该含有DB操作（该项目会从DB中读取数据写入到view中）
 */
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Storage;

class CreateStaticHtmlFile
{


    /**
     * @power 请求进入该构造函数，该函数判断请求应该进入laravel还是laravel的storage下的一个文件，若进入文件这表明该文件没有失效，如果该文件不存在这进入laravel并生产文件
     * @return void
     */
    public function __construct()
    {
//        $storage->disk('local')->put(__LINE__ . '.html', __LINE__);
//        dd($storage == app()['filesystem']);
//        die;
//        \Illuminate\Support\Facades\Storage::disk('local')->put(__LINE__ . '.html', __LINE__);
//        dd($filesystem->put('1.html', '22'));
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $path = $request->path();
        $storage = new Storage();

        if (is_file(storage_path('app/' . $path))) {
             $response = response($storage->disk('local')->get($path));
        } else {
            $response = $next($request);
            $storage->disk('local')->put($path, $response . '<!--date' . date('Y-m-d H:i:s', intval(LARAVEL_START)) . '-->');
        }
        unset($storage);
        return $response;
    }


}
