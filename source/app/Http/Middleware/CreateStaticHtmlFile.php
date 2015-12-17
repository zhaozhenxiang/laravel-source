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
        app()->bind('storage', function(){
            return new Storage();
        });
        $this->delete();
        //我们认为get请求是来生成文件的
        //@todo 可以把get post 合并到一个函数中。 使用laravel的任务调度来使文件过期掉(delete)
        //@todo 可以使用注释来设置一下信息：过期时间、使用什么cachedriver
        switch (strtolower($request->method())) {
            case 'get':
                return $this->create($request, $request, $next);
                break;
            default:
                $this->delete($request);
                break;
        }
        return $next($request);
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    private function create($request, $request, $next)
    {
        $path = $request->path();
        $storage = app()['storage'];

        if (is_file(storage_path('app/' . $path))) {
            $response = response($storage->disk('local')->get($path));
        } else {
            $response = $next($request);
            $storage->disk('local')->put($path, $response . '<!--date' . date('Y-m-d H:i:s', intval(LARAVEL_START)) . '-->');
        }
        unset($storage);
        return $response;
    }

    /**
     * @power 判断是否需要去delete掉一个文件
     * @return bool
     */
    private function delete()
    {
        //获取router类实例
        $router = app()['router'];
        //获取当前route实例
        $route = app()['router']->current();

/*        //这一步也可以不判断
        if (in_array('GET', $route->getMethods()) || in_array('get', $route->getMethods()) ) {
            return FALSE;
        }*/

        //获取当前action信息
        /**形如
         array:6 [▼
            "middleware" => "CreateStaticHtmlFile"
            0 => Closure {#107 ?}
            "uses" => Closure {#107 ?}
            "namespace" => "App\Http\Controllers"
            "prefix" => null
            "where" => []
            ]
         */
        $action = $route->getAction();

        if (!is_string($action['uses'])) {
            return FALSE;
        }

        list($class, $method) = explode('@', $action['uses']);

        //反射到class
        //@help http://php.net/manual/en/class.reflectionclass.php
        $classer = new \ReflectionClass($class);
        //获取methods
        //@help http://php.net/manual/zh/class.reflectionmethod.php
        $methoder = $classer->getMethod($method);
        //获取方法的注释
        $doc = $methoder->getDocComment();

/*        //解析方法注释
        $array = explode('@expire:', $doc);

        if (2 == count($array)) {
            return $array;
        }*/

        preg_match('/(?<=\@expire:date=).+?(?=\n+)/', $doc, $out);

        if (!is_array($out)) {
            return FALSE;
        }

        //删除操作
        if (strtotime($out[0]) <= LARAVEL_START) {
            return app()['storage']->disk('local')->delete($route->getUri());
        }
    }


}
