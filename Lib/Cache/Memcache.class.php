<?php
/*
 * Memcache 管理类
 * 
 * 单机模式
 * 
 */

namespace Lib\Cache;
use Lib\Log;
use Lib\Exception;
use Lib\Timer;
use memcached;
class Memcache{
    protected $connection = null;
    protected $prefix = null;
    protected $config = array();
    protected $handle = null;
    protected $timeLong = 0.2;
    
    public function __construct($config = array(), $prefix = ''){
        if(!extension_loaded('memcached'))
            throw new Exception('memcached not suport');
        $this->config = empty($config)?$GLOBALS['conf']['Memcache']:$config;
        $this->config = array_values($this->config);
        $this->prefix = empty($prefix)?$GLOBALS['conf']['MemcachePrefix']:$prefix;
        $this->handle = new memcached();
        $this->handle->addServers($this->config);
    }
    public function __call($method, $args){
        if(!method_exists($this->handle, $method))
            throw new Exception('Memcache method ' . $method . 'not defined');
        $timer = new Timer();
        $result = null;
        try{
            $timer->start();
            $result = call_user_func_array(array($this->handle,$method), $args);
            $timer->end();
            $time = $timer->getTime();
            if($time > $this->timeLong)
                Log::write();
        }catch(Exception $e){
            Log::write('Memcache error execute ' . $method . ' at line ' . __LINE__);
        }
        $timer = null;
        return $result;
    }
    
}