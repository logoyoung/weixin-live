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
//Lib
defined('LIB') or define('LIB', ROOT . 'Lib/');
//Controller


//获取系统环境变量
//获取主机名
$host = gethostname();
//获取机器部署方案
$rules = require 'host.php';
$env = 'dev';
foreach ($rules as $node => $rule){
    if(preg_match($rule, $host)){
        $env = $node;
    }
}
$configFile = LIB . "$env.php";
if(file_exists($configFile)){
    $GLOBALS['conf'] = require $configFile;
}
unset($env);

//类文件简易自动加载
spl_autoload_register(function($class){
    //加载文件
    if(false !== $pos = strripos($class, '\\')){
        $classPath = str_replace('\\', '/', substr($class, 0, $pos)).'/';
        $className    = substr($class, $pos + 1);
    }else {
        $classPath ='';
        $className = $class;
    }
    $classPath      = ROOT . $classPath;
    $classPath      .= str_replace('_', '/', $className);//var_dump($classPath);
    if(file_exists($classPath . '.class.php')){
        require $classPath . '.class.php';
    }elseif( file_exists($classPath . '.php') ) {
        require $classPath . '.php';
    }
});

use Lib\Router;
use Lib\Dispatcher;

$router = new Router();
$router->setNamespace('\\' . DEFAULT_MODEL);
$dispatcher = new Dispatcher();
$dispatcher->setRouter($router);
//$dispatcher->setCallback(self::$callback);
$dispatcher->run();


