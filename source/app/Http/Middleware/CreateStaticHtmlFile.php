<?php

/**
 * @idea ���ھ�̬�ļ����͵�cached�Ǻ������ģ���ҳ��ֻ�ʺ������ҳ��
 * @idea ������Ϊ·����û�в�����·��Ӧ�ò�����DB����������Ӧ�����DB�ж�ȡ����д�뵽view�У�
 * @idea ������Ϊ·�����в�����·��Ӧ�ú���DB����������Ŀ���DB�ж�ȡ����д�뵽view�У�
 */
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Storage;

class CreateStaticHtmlFile
{


    /**
     * @power �������ù��캯�����ú����ж�����Ӧ�ý���laravel����laravel��storage�µ�һ���ļ����������ļ���������ļ�û��ʧЧ��������ļ������������laravel�������ļ�
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
        //������Ϊget�������������ļ���
        //@todo ���԰�get post �ϲ���һ�������С� ʹ��laravel�����������ʹ�ļ����ڵ�(delete)
        //@todo ����ʹ��ע��������һ����Ϣ������ʱ�䡢ʹ��ʲôcachedriver
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
     * @power �ж��Ƿ���Ҫȥdelete��һ���ļ�
     * @return bool
     */
    private function delete()
    {
        //��ȡrouter��ʵ��
        $router = app()['router'];
        //��ȡ��ǰrouteʵ��
        $route = app()['router']->current();

/*        //��һ��Ҳ���Բ��ж�
        if (in_array('GET', $route->getMethods()) || in_array('get', $route->getMethods()) ) {
            return FALSE;
        }*/

        //��ȡ��ǰaction��Ϣ
        /**����
         array:6 [��
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

        //���䵽class
        //@help http://php.net/manual/en/class.reflectionclass.php
        $classer = new \ReflectionClass($class);
        //��ȡmethods
        //@help http://php.net/manual/zh/class.reflectionmethod.php
        $methoder = $classer->getMethod($method);
        //��ȡ������ע��
        $doc = $methoder->getDocComment();

/*        //��������ע��
        $array = explode('@expire:', $doc);

        if (2 == count($array)) {
            return $array;
        }*/

        preg_match('/(?<=\@expire:date=).+?(?=\n+)/', $doc, $out);

        if (!is_array($out)) {
            return FALSE;
        }

        //ɾ������
        if (strtotime($out[0]) <= LARAVEL_START) {
            return app()['storage']->disk('local')->delete($route->getUri());
        }
    }


}
