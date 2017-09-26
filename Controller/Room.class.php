<?php
namespace Controller;

use Lib\Controller;
use Lib\Db;
use Lib\Cache;
use memcached;
use Lib\Smarty\Smarty;
use Cli\Message;

class Room extends Controller{
    
    protected $roomid = 100001;
    
    public function index($roomid){
        $roomid = empty($roomid)?$this->roomid:$roomid;
        //get room users
        $users = Message::getRoomUsers($roomid);
        $conf = $GLOBALS['conf']['stream'];
        $stream = $conf['hls'] . $conf['stream'] . '.m3u8';
        $cmd = "/usr/bin/curl -i -s -w %{http_code} --connect-timeout 10 -m 10 \"{$stream}\"";
        $r = `$cmd`;
        if(!strtr($r, '200')){
            ;//$stream = "";
        }
        $smarty = new Smarty();
        $smarty->assign('roomid',$roomid);
        $smarty->assign('users',$users);
        $smarty->assign('test','test');
        $smarty->assign('stream',$stream);
        $smarty->display('room.html');
    }
    public function test(){
        $redis = Cache::getInstance();
        //$redis->sadd('200','xixi');
        $users = Message::getRoomUsers('100');
        //$users = $redis->smembers('200');
        $gifts = Message::getRoomGifts('100');
       // var_dump($users);
        var_dump($gifts);
    }
}