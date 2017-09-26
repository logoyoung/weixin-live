<?php
namespace Controller;

use Lib\Controller;
use Lib\Db;
use Lib\Cache;
use memcached;
use Lib\Smarty\Smarty;
class Test extends Controller{
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
}