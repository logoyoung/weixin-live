<?php
/**
 * 系统入口文件
 * 
 */
date_default_timezone_set('Asia/Shanghai');
//记录系统开始时间
$GLOBALS['startTime'] = microtime(true);

define('CLI', PHP_SAPI == 'cli' ? true : false);

//直播系统入口常量设置
defined('LIVE') or define('LIVE', 'LIVE');
//获得根目录
defined('ROOT') or define('ROOT', dirname( __DIR__ ) . '/');
//www目录
defined('WWW') or define('WWW', ROOT . 'htdocs/');


//获取系统环境变量
//获取主机名
$host = gethostname();
//获取机器部署方案
$rules = require 'host.php';
$env = 'pro';
foreach ($rules as $node => $rule){
    if(preg_match($rule, $host)){
        $env = $node;
    }
}

if(file_exists($env . '.php')){
    $GLOBALS['conf'] = require $env . '.php';
}
unset($env);
  
//类文件简易自动加载
spl_autoload_register(function($class){
    //加载文件
    if(flase !== $pos = strripos($class, '\\')){
        $classPath = str_replace('\\', '/', substr($class, 0, $pos)).'/';
        $className    = substr($class, $pos + 1);
    }else {
        $classPath ='';
        $className = $class;
    }
    $classPath      .= ROOT;
    $classPath      .= str_replace('_', '/', $className).'class.php';
    if(file_exists($classPath)){
        require $classPath;
    }
});

use Router;
use Lib\Dispatcher;

$router = new Router();
//$router->setNamespace(self::$namespace);
$dispatcher = new Dispatcher();
$dispatcher->setRouter($router);
//$dispatcher->setCallback(self::$callback);
$dispatcher->run();


