<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Http\Request;

class StaticCache extends Job implements SelfHandling
{

    //Request实例
    private $request;

    //闭包
    private $closure;

    //注释代码
    private $doc = '';

    //前缀变量
    private $prefix = '';

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Request $request, \Closure $closure)
    {
        $this->request = $request;
        $this->closure = $closure;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        app()->bind('storage', function(){
            return new \Storage();
        });
        //不能使用$this->closure()这种调用方式
        $closure = $this->closure;
        $docParser = $this->docParser();

        //过期时间小于当前时间=》不使用cache策略
        if (strtotime($docParser['expire']) <= LARAVEL_START) {
            return $closure($this->request);
        }
        //@todo 执行delete函数之前应该先根据函数注释的代码来判断该response需不需要使用cached
        if ($this->delete()) {
            return $this->create($this->request, $this->closure);
        }
        return $this->readCached($this->getPath());
    }

    /**
     * @power 读取cache
     * @return string
     */
    private function readCached($path)
    {
        if ('file' == env('STATIC_DRIVER')) {
            $response = file_get_contents($path);
        } elseif ('db' == env('STATIC_DRIVER')) {
            $response = $this->getInfoByDB($path);
            if (!empty($response)) {
                $response = $response[0]['content'];
            } else {
                $response = '';
            }
        }

        return $response;
    }
    /**
     * @power 写入
     * @return string
     */
    private function create($request, $next)
    {
        //@todo 这里的path也要根据STATIC_DRIVER做出变化
        $path = $this->getPath();
        $doc = $this->docParser();

        if ('file' == env('STATIC_DRIVER')) {
            $response = $this->writeFile($request, $next, $path);
        } elseif ('db' == env('STATIC_DRIVER')) {
            $response = $this->writeDB($request, $next, $path);
        }

        return $response;
    }

    /**
     * @power 写入文件
     * @return Response
     */
    private function writeFile($request, $next, $path)
    {
        $storage = new \File();

        if (is_file($path)) {
            $response = response($storage->get($path));
        } else {
            $response = $next($request)->getContent();
            $storage->put($path, $response . '<!--date' . date('Y-m-d H:i:s', intval(LARAVEL_START)) . '-->');
        }

        return $response;
    }

    /**
     * @return Response
     */
    private function writeDB($request, $next, $path)
    {
        $storage = new \DB();
        $info = $this->getInfoByDB($path);

        if (!empty($info)) {
            return $info[0]['content'];
        }

        $response = $next($request)->getContent();
        $storage->insert('INSERT INTO `cached`(`id`, `init_time`, `path`, `content`) VALUES(UUID(), SYSDATE(), ?, ?)', [$path, $response]);

        return $response;
    }

    /**
     * @power 获取注释
     * @return bool|string
     */
    private function getDoc()
    {
        //不为空就返回
        if ('' != $this->doc) {
            return $this->doc;
        }

        //获取当前route实例
        $route = app()['router']->current();

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
            return '';
        }

        list($class, $method) = explode('@', $action['uses']);

        //反射到class
        //@help http://php.net/manual/en/class.reflectionclass.php
        $classer = new \ReflectionClass($class);
        //获取methods
        //@help http://php.net/manual/zh/class.reflectionmethod.php
        $methoder = $classer->getMethod($method);
        //获取方法的注释
        $this->doc = $methoder->getDocComment();

        return $this->doc;
    }

    /**
     * @power 获取该响应对应的文件路径
     */
    private function getPath()
    {
        if ('file' == env('STATIC_DRIVER')) {
            $dir = !empty(env('STATIC_PATH')) ? base_path(env('STATIC_PATH')) : storage_path('app');
            $urlPath = $this->request->path();

            if ('/' == $urlPath[strlen($urlPath) - 1]) {
                $urlPath .= 'index';
            }

            $dirName = '';
            if ('.' != dirname($urlPath)) {
                $dirName = dirname($urlPath) . DIRECTORY_SEPARATOR;
            }

            $path = $dir . DIRECTORY_SEPARATOR . $dirName . $this->getPrefix() . basename($urlPath);
        } elseif ('db' == env('STATIC_DRIVER')) {
            $path = $this->request->path();
        }

        return $path;
    }

    /**
     * @power 判断是否需要去delete掉一个文件
     * @return bool TRUE:删除掉了文件 FALSE:没删除掉
     */
    private function delete()
    {
        $path = $this->getPath();
        $docParser = $this->docParser();

        if ('file' == env('STATIC_DRIVER')) {
            return $this->deleteFile($path, $docParser);
        } elseif ('db' == env('STATIC_DRIVER')) {
            return $this->deleteDB($path, $docParser);
        }
    }

    /**
     * @power 删除DB
     * @return bool
     */
    private function deleteDB($path, $docParser)
    {
        $info = $this->getInfoByDB($path);

        if (empty($info)) {
            return TRUE;
        }
        $storage = new \DB;
        if (($docParser['max'] + strtotime($info[0]['init_time'])) <= LARAVEL_START) {
            return $storage->delete('DELETE FROM `cached` WHERE `path` = ?', [$path]);
        }

        return FALSE;
    }

    private function getInfoByDB($path)
    {
        return \DB::SELECT('SELECT * FROM `cached` WHERE `path` = ?', [$path]);
    }

    /**
     * @power 删除文件
     * @return bool
     */
    private function deleteFile($path, $docParser)
    {
        //不存在文件就无法删除
        if (!is_file($path)) {
            return TRUE;
        }

        //删除操作
        if (($docParser['max'] + filemtime($path)) <= LARAVEL_START ) {
            return (new \File)->delete($path);
        }

        return FALSE;
    }

    /**
     * @power获取doc的解析之后的数组
     * @return array
     */
    private function docParser()
    {
        $doc = $this->getDoc();
        $value = [];
        foreach ($this->templateVar() as $key => $val) {
            preg_match($val, $doc, $$key);
            if (empty($$key)) {
                $$key = [''];
            }
            array_push($value, ${$key}[0]);
        }

        return array_combine(array_keys($this->templateVar()), $value);
    }

    /**
     * @power 解析模板
     * @todo 下一步需要做到把这个方法扩展成一个class
     * @return array
     */
    private function templateVar()
    {
        return [
            'expire' => '/(?<=\@expire:date=).+?(?=\r+)/',
            'max' => '/(?<=\@max:age=).+?(?=\r+)/'
        ];
    }

    /**
     * @power 获取变量前缀
     * @return mixed|string
     */
    private function getPrefix()
    {
        if (empty($this->prefix)) {
            $this->prefix = env('STATIC_PREFIX');
        }

        return $this->prefix;
    }
}
