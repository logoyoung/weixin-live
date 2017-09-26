<?php
/*
 * redis 操作类
 */
namespace Lib\Cache;
use Lib\Log;
use Lib\Exception;
use Lib\Timer;
use redis;

class Redishelper{
    protected $connections = array();
    protected $_connection = null;
    protected $master = false;
    protected $prefix = '';
    protected $try = 1;
    protected $config = array();
    protected $connectTime = 0.2;
    
    
    public function __construct(array $config = array(), $prefix = ''){
        
        $this->config = empty($config)?$GLOBALS['conf']['Redis']:$config;
        $this->prefix = empty($prefix)?$GLOBALS['conf']['RedisPrefix']:$prefix;
        //$this->master = is_bool($master)?$master:false;
    }
    
    public function connect($master = false){
        $config = $this->getRedisConfig($master);
        $redisKey = md5(serialize($config));
        if(!empty($this->connections[$redisKey])){
            $this->_connection = $this->connections[$redisKey];
            //if(IS_CLI) return $this->_connection;
            try {
                if($this->_connection->ping()) return $this->_connection;
            }catch(Exception $e){
                Log::write('redis connection timeout');
            }
            unset($this->connections[$redisKey]);
            $this->_connection = null;
        }
        $timer = new Timer();
        $timer->start();
        $this->_connection = new redis();
        $status = $this->_connection->connect($config['ip'],$config['port'],$config['timeout'],$config['auth']);
        $timer->end();
        $time = $timer->getTime();
        $timer = null;
        if(!$status){
            Log::write('connect error');
            $this->_connection = null;
            return false;
        }
        $this->connections[$redisKey] = $this->_connection;
        if($time > $this->connectTime)
            Log::write('redis connect time:' . $time);
        return $this->_connection;
    }
    public function setMaster($master = true){
        $this->master = $master;
    }
    public function getRedisConfig($master = false){
        if($master)
            return $this->config[0];
        return $this->config[mt_rand(1, count($this->config)-1)];
    }
    
    public function __call($method,$args){
        $args[0] = $this->prefix . $args[0];
        $try = $this->try;
        $result = false;
        $master = $this->isMaster($method) || $this->master;
        $timer = new Timer();
        $timer->start();
        do{
           try {
               $this->connect($master);
               if(!$this->_connection || !method_exists($this->_connection, $method)){
                   $timer = null;
                   Log::write('redis connect failed or method ' . $method . ' not exist');
                   return $result;
               }
               $result = call_user_func_array(array($this->_connection,$method), $args);
               $timer->end();
               $time = $timer->getTime();
               if($time > $this->connectTime)
                   Log::write('redis ' . $method . 'execute time:' . $time);
           }catch(Excetion $e){
               $config = $this->getRedisConfig($master);
               $redisKey = md5(serialize($config));
               if(!empty($this->connections[$redisKey])){
                   $this->_connection = null;
                   unset($this->connections[$redisKey]);
                   Log::write($e->getMessage());
               }
           }
           
        }while($try-- > 0);
        $timer = null;
        return $result;
    }
    
    public function isMaster($method){
        $methods = array(
            'del'         => 1,
            'delete'      => 1,
            'expire'      => 1,
            'expireat'    => 1,
            'move'        => 1,
            'persist'     => 1,
            'rename'      => 1,
            'renamenx'    => 1,
            'sort'        => 1,
            'append'      => 1,
            'set'         => 1,
            'decr'        => 1,
            'decrby'      => 1,
            'getset'      => 1,
            'incr'        => 1,
            'incrby'      => 1,
            'mset'        => 1,
            'msetnx'      => 1,
            'setbit'      => 1,
            'setex'       => 1,
            'setnx'       => 1,
            'setrange'    => 1,
            'hdel'        => 1,
            'hincrby'     => 1,
            'hmset'       => 1,
            'hset'        => 1,
            'hsetnx'      => 1,
            'blpop'       => 1,
            'brpop'       => 1,
            'brpoplpush'  => 1,
            'linsert'     => 1,
            'lpop'        => 1,
            'lpush'       => 1,
            'lpushx'      => 1,
            'lrem'        => 1,
            'lremove'     => 1,
            'lset'        => 1,
            'rpop'        => 1,
            'rpoplpush'   => 1,
            'rpush'       => 1,
            'rpushx'      => 1,
            'sadd'        => 1,
            'smove'       => 1,
            'spop'        => 1,
            'srem'        => 1,
            'sunionstore' => 1,
            'zadd'        => 1,
            'zincrby'     => 1,
            'zinterstore' => 1,
            'zrem'        => 1,
            'zremrangebyrank'  => 1,
            'zremrangebyscore' => 1,
            'zunionstore' => 1,
            'multi'       => 1,
            'exec'        => 1,
            'ltrim'       => 1,
        );
        return isset($methods[$method]);
    }
    
}