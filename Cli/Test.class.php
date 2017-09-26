<?php
namespace Cli;

use Lib\Controller;
use Lib\Db;
use Lib\Cache;
use memcached;
use Lib\Smarty\Smarty;
use Workerman\Worker;
use Workerman\Autoloader;
use Workerman\Channel\Client;
use Workerman\Channel\Server;


class Test extends Controller{
    
    public function __construct(){
        //new Autoloader();
    }
    
    
    public function test(){
        echo "hellow world\n";
        var_dump($_GET);
    }
    public function info(){
        phpinfo();
    }
    public function db(){
        $db = Db::getDb();
        $resulrs = $db->query('select * from live');
        var_dump($resulrs);
    }
    public function insert(){
        $db = Db::getDb();
        $res = $db->execute("insert into live(`name`) values(?)",array('jiji'));
        
        $affectRow = $db->getAffectRow();
        $lastInsertId = $db->getLastId();
        var_dump($lastInsertId);
        var_dump($affectRow);
    }
    public function redis(){
        //phpinfo();exit;
        $redis = Cache::getInstance();
        $redis->set('test','test');
        //sleep(1);
        $v = $redis->get('test');
        var_dump($redis);
        var_dump($v);
    }
    public function memcache(){
        /*$m = new memcached();
        $m->addServers(array(
            array('127.0.0.1','11211'),
            array('127.0.0.1','11212'),
        ));
        
        $m->set('int', 99);
        $m->set('string', 'a simple string');
        $m->set('array', array(11, 12));
        //'object'这个key将在5分钟后过期 
        //$m->set('object', new stdclass, time() + 300);
        
        
        var_dump($m->get('int'));
        var_dump($m->get('string'));
        var_dump($m->get('array'));
        //var_dump($m->get('object'));
        exit;
        */
        
        $memcached = Cache::getInstance('Memcache');
        //$memcached->set('test','test');
        $v = $memcached->get('test');
        var_dump($v);
    }
    
    public function smarty(){
        $smarty = new Smarty();
        $smarty->assign('sd','df');
        $smarty->display('threejs/horse.html');
    }
    
    
    public function worker(){
        
        require_once '../Workerman/Autoloader.php';
        
        /*$worker = new Worker('websocket://0.0.0.0:8484');
        // 直接设置所有连接的onMessage回调
        $worker->onMessage = function($connection, $data)
        {
            $connection->send('hellow logoyoung');
            sleep(10);
            $connection->send('hahah');
        };
        // 运行worker
        Worker::runAll();*/
        
        $global_uid = 0;
        
       
        
        // 创建一个文本协议的Worker监听2347接口
        $text_worker = new Worker("websocket://0.0.0.0:2347");
        
        // 只启动1个进程，这样方便客户端之间传输数据
        $text_worker->count = 1;
        
        $text_worker->onConnect = function ($connection)
        {
            global $text_worker, $global_uid;
            // 为这个链接分配一个uid
            $connection->uid = ++$global_uid;
        };
        $text_worker->onMessage = function ($connection, $data)
        {
            global $text_worker;
            foreach($text_worker->connections as $conn)
            {
                $conn->send("user[{$connection->uid}] said: $data");
            }
        };
        $text_worker->onClose = function ($connection)
        {
            global $text_worker;
            foreach($text_worker->connections as $conn)
            {
                $conn->send("user[{$connection->uid}] logout");
            }
        };
        
        Worker::runAll();
    }
    
    public function channel(){
        //require_once '../Workerman/Autoloader.php';
        // 初始化一个Channel服务端
        $channel_server = new Server('0.0.0.0', 2206);
        
        // websocket服务端
        $worker = new Worker('websocket://0.0.0.0:4236');
        $worker->name = 'websocket';
        $worker->count = 6;
        // 每个worker进程启动时
        $worker->onWorkerStart = function($worker)
        {
            // Channel客户端连接到Channel服务端
            Client::connect('127.0.0.1', 2206);
            // 订阅broadcast事件，并注册事件回调
            Client::on('broadcast', function($event_data)use($worker){
                // 向当前worker进程的所有客户端广播消息
                foreach($worker->connections as $connection)
                {
                    $connection->send($event_data);
                }
            });
        };
        
        $worker->onMessage = function($connection, $data)
        {
            // 将客户端发来的数据当做事件数据
            $event_data = $data;
            // 向所有worker进程发布broadcast事件
            Client::publish('broadcast', $event_data);
        };
        
        Worker::runAll();
    }
    public function xx(){
        //require_once '../Workerman/Autoloader.php';
        // 初始化一个Channel服务端
        $channel_server = new Server('0.0.0.0', 2207);
    
        // websocket服务端
        $worker = new Worker('text://0.0.0.0:4237');
        //$worker->name = 'websocket23';
        $worker->count = 6;
        // 每个worker进程启动时
        $worker->onWorkerStart = function($worker)
        {
            // Channel客户端连接到Channel服务端
            Client::connect('127.0.0.1', 2207);
            // 订阅broadcast事件，并注册事件回调
            Client::on('broadcast', function($event_data)use($worker){
                // 向当前worker进程的所有客户端广播消息
                foreach($worker->connections as $connection)
                {
                    $connection->send($event_data);
                }
            });
        };
    
        $worker->onMessage = function($connection, $data)
        {
            // 将客户端发来的数据当做事件数据
            $event_data = $data;
            // 向所有worker进程发布broadcast事件
            Client::publish('broadcast', $event_data);
        };
    
        Worker::runAll();
    }
    
    
}