<?php

/**
 * @idea ���ھ�̬�ļ����͵�cached�Ǻ������ģ���ҳ��ֻ�ʺ������ҳ��
 * @idea ������Ϊ·����û�в�����·��Ӧ�ò�����DB����������Ӧ�����DB�ж�ȡ����д�뵽view�У�
 * @idea ������Ϊ·�����в�����·��Ӧ�ú���DB����������Ŀ���DB�ж�ȡ����д�뵽view�У�
 */
namespace App\Http\Middleware;

use Closure;

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
        $response = $next($request);
//        dd($response->getContent());
    }
}
