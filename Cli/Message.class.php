<?php

/**
 * 
 * 消息模块
 * 
 */


namespace Cli;

use Lib\Controller;
use Lib\Cache;
use Workerman\Autoloader;
use Workerman\Worker;
use Workerman\Channel\Client;
use Workerman\Channel\Server;
use Lib\Exception;
use Lib\Log;


class Message extends Controller{
    
    //房间地址
    static $room = 'room\/';
    static $gift = 'gift\/';
    static $redis = null;
    //礼物
    static $giftType = array(
        //直播礼物
        '101' => array(),
        '102' => array(),
        '103' => array(),
        '104' => array(),
        '105' => array(),
    );
    //频道事件
    static $events = array(
        'broadcast',
        'unicast',
        'gift',
        'room',
        'other',
    );
    //消息格式
    static $msg = array(
        'mid'=>'',
        'uid'=>'',
        'tid'=>'',
        'content'=>'',
        'channel'=>'',
        'ctime'=>'',       
    );
    
    //websocket service
    const WEBSOCKET = 'websocket://0.0.0.0:4326';
    //worker name
    const WORKER_NAME = 'websocket';
    //channel service,client
    const CHANNEL_SERVER = '0.0.0.0:2260';
    const CHANNEL_CLIENT = '127.0.0.1:2260';
    //process number
    const PROCESS = 4;
     
    //系统广播
    public static function broadcast($data, $worker){
        foreach ($worker->connections as $connection){
            $connection->send(json_encode($data));
        }
    }
    //系统单播
    public static function unicast($data, $worker){
        
    }
    //礼物
    public static function gift($user, $worker){
        //get roomid
        $roomid = empty($user['tid'])?'':$user['tid'];
        if(!$roomid){
            Log::write('no roomid');
            return false;
        }
        if(!self::$redis)
            self::$redis = Cache::getInstance();
        //get roomkey
        $roomkey = self::$gift . $roomid;
        //get room users
        //$users = self::$redis->lrange($roomkey, 0, -1);
         
        //send msg to myself
        //$users[] = json_encode($user);
        //add user into room list
        //$roomkey = self::$room . $roomid;
        $user = json_encode($user);
        try{
            $r = self::$redis->lpush($roomkey, $user);var_dump($r);
        }catch(Exception $e){
            Log::write();
            throw $e;
        }
        //send message to room users
        foreach ($worker->connections as $connection){
            $data = json_decode($connection->data,true);
            if(('gift' == $data['channel']) && ($roomid == $data['tid']))
                $connection->send(json_encode($user));
        }
    }
    //进入房间
    public static function enterroom($user, $worker){
        //get roomid
        $roomid = empty($user['tid'])?'':$user['tid'];
        if(!$roomid){
            Log::write('no roomid');
            return false;
        }
        if(!self::$redis)
            self::$redis = Cache::getInstance();
        //get roomkey
        $roomkey = self::$room . $roomid;
        //get room users
        //$users = self::$redis->lrange($roomkey, 0, -1);
       
        //send msg to myself
        //$users[] = json_encode($user);
        //add user into room list
        //$roomkey = self::$room . $roomid;
        $user = json_encode($user);
        try{
            $r = self::$redis->sadd($roomkey, $user);
        }catch(Exception $e){
            Log::write();
            throw $e;
        }
        //send message to room users
        foreach ($worker->connections as $connection){
            $data = json_decode($connection->data,true);
            if(('room' == $data['channel']) && ($roomid == $data['tid']))
                $connection->send(json_encode($user));
        }
    }
    //离开房间
    public static function leaveroom($user, $worker){
        //get roomid
        $roomid = empty($user['tid'])?'':$user['tid'];
        if(!$roomid){
            Log::write('no roomid');
            return false;
        }
        if(!self::$redis)
            self::$redis = Cache::getInstance();
        //get roomkey
        $roomkey = self::$room . $roomid;
        //get room users
        //$users = self::$redis->lrange($roomkey, 0, -1);
         
        //send msg to myself
        //$users[] = json_encode($user);
        //add user into room list
        //$roomkey = self::$room . $roomid;
        $user = json_encode($user);
        try{
            self::$redis->srem($roomkey, $user);
        }catch(Exception $e){
            Log::write();
            throw $e;
        }
        //send message to room users
        foreach ($worker->connections as $connection){
            $data = json_decode($connection->data,true);
            if(('room' == $data['channel']) && ($roomid == $data['tid']))
                $connection->send($user);
        }
        
    }
    
    //其他
    public static function other($user, $worker){
        foreach ($worker->connections as $connection)
            $connection->send('I am recived');
    }
 
    public function run(){
        // 初始化一个Channel服务端
        
        list($serverip,$serverport) = explode(':', self::CHANNEL_SERVER);
        list($clientip,$clientport) = explode(':', self::CHANNEL_CLIENT);
        $channel_server = new Server($serverip, $serverport);
        
        // websocket服务端
        $worker = new Worker(self::WEBSOCKET);
        $worker->name = self::WORKER_NAME;
        $worker->count = self::PROCESS;
        $worker->onWorkerStart = function($worker)use($clientip,$clientport)
        {
            // Channel客户端连接到Channel服务端
            Client::connect($clientip, $clientport);
            
            //注册事件
            // 广播
            Client::on('broadcast', function($event_data)use($worker){
                self::broadcast($event_data,$worker);
            });
            //单播
            Client::on('unicast', function($event_data)use($worker){
                self::broadcast($event_data,$worker);
            });
            //礼物
            Client::on('gift', function($event_data)use($worker){
               self::gift($event_data, $worker); 
            });
            //房间消息
            Client::on('room', function($event_data)use($worker){
                self::enterroom($event_data, $worker);
            });
            //其他消息
            Client::on('other',function($event_data)use($worker){
                self::other($event_data, $worker);
            });
            //离开房间
            Client::on('leaveroom',function($event_data)use($worker){
                self::leaveroom($event_data, $worker);
            });
        };
        //接收
        $worker->onMessage = function($connection, $data)
        {      
            global $mid ;  
            // 将客户端发来的数据当做事件数据
            $event_data = json_decode($data, true);
            $event_data['mid'] = md5(++$mid); 
            $event_data['ctime'] = time();
            
            //bind data to connection
            if(self::fitler($event_data))
                $connection->data = json_encode($event_data);
            else 
            {
                Log::write('invalid data');
                return false;
            }
            /*'mid'=>'',
            'type'=>'',
            'uid'=>'',
            'tid'=>'',
            'content'=>'',
            'ctime'=>'',
            //bind data to connection
            $connection->data = json_decode($event_data);*/
            //$connection->id = md5(time() . rand(0, 99999999));
            /*$connection->type = $event_data['type'];
            $connection->uid  = $event_data['content']['uid'];
            $connection->roomid  = $event_data['content']['roomid'];
            $connection->username = $event_data['content']['username'];
            $event_data['type'] = empty($event_data['type'])?'other':$event_data['type'];
            $event_data['ctime'] = time();*/
            
            
            // 向所有worker进程发布事件
            if(in_array($event_data['channel'], self::$events))
                Client::publish($event_data['channel'], $event_data);
        };
        
        $worker->onClose = function($connection)use($worker)
        {
            $data = json_decode($connection->data,true);
            if('room' == $data['channel']){
                //self::leaveroom($data, $worker);
                Client::publish('leaveroom', $data);
            }
                
        };        
        Worker::runAll();
    }
    
    public static function fitler($data){
        //simple fitler
        $msgkey = array_diff_key(self::$msg, $data);
        if(!empty($msgkey)) return false;
        return true;
    }
    
    public static function getRoomKey($roomid = ''){
        if(!$roomid) return false;
        return  self::$room . $roomid;
    }
    public static function getGiftKey($roomid = ''){
        if(!$roomid) return false;
        return  self::$gift . $roomid;
    }
    public static function getRoomUsers($roomid = ''){
        if(!$roomid) return false;
        $roomKey =  self::getRoomKey($roomid);
        if(!self::$redis)
            self::$redis = Cache::getInstance();
        $users = self::$redis->smembers($roomKey);
        return $users;
    }
    public static function getRoomGifts($roomid = ''){
        if(!$roomid) return false;
        $giftKey =  self::getGiftKey($roomid);
        if(!self::$redis)
            self::$redis = Cache::getInstance();
        $gifts = self::$redis->lrange($giftKey,0,-1);
        return $gifts;
    }
}