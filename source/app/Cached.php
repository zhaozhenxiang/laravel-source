<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cached extends Model
{
    //表
    protected $table = 'cached';
    //字段
    protected $fillable = ['id', 'init_time', 'path', 'content'];
    //不启用时间戳
    public $timestamps=false;

    /**
     * @power 获取ID
     */
    public function getID()
    {
        return $this->id;
    }

    /**
     * @power 获取PATH
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @power 获取TIME
     */
    public function getInitTime()
    {
        return $this->init_time;
    }

    /**
     * @power 获取CONTENT
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @power 重写父类的insert方法
     * @param array
     */
    public function insertAction(array $info)
    {
        if (2 !== count($info)) {
            return FALSE;
        }

        if (!isset($info['path'], $info['content'])) {
            return FALSE;
        }

        //@idea 这里也可以使用mysql内置的函数 UUID & DATE
        $info['id'] = getUUID();
        $info['init_time'] = date('Y-m-d H:i:s', time());

        return parent::insert($info);
    }

    /**
     * @power call\callstatic模式方法调用的实际函数
     * @param $method
     * @param $parameters
     * @return bool|mixed
     */
    private function call($method, $parameters, $instance)
    {
        switch ($method) {
            case 'insert':
                return $instance->insertAction($parameters[0]);
                break;

            default:
                break;
        }

        return parent::__callStatic($method, $parameters);
    }

    /**
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
        $instance = new Static;
        return $instance->call($method, $parameters, $instance);
    }
}