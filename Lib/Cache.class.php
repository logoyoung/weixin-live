<?php
/*
 * 缓存驱动
 */
namespace Lib;
use Lib\Log;
use Lib\Exception;

class Cache {
    private static   $instance = array();
    private static   $handle = null;
    
    public static  function getInstance($type = 'redishelper'){
        $class = ucwords(strtolower($type));
        if(isset(self::$instance[$type])){
            self::$handle = self::$instance[$type];
            return self::$handle;
        }
        $class = 'Lib\\Cache\\' . $class;
        if(class_exists($class)){
            self::$instance[$type] = new $class();
            self::$handle = self::$instance[$type];
            return self::$handle;
        }
        throw new Exception('class ' . $class . 'not exists');
    }
     
    /*protected function setLog($msg){}
    protected function getLog($msg){}
    public function get($key, $value){}
    public function set($key, $value){}
    public function del($key, $value){}*/
    public function __call($method,$args){
        if(method_exists(self::$handle, $method)){
            return call_user_func_array(array(self::$handle,$method), $args);
        }
        else{
            //Log::write();
            throw new Exception('call not defined menthod ' . get_class(self::handle) . ':' . $method);
        }
    }
    
}