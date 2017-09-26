<?php
/**
 * 开发环境配置
 */

define('DEBUG', true);

return array(
    /*********************DB*****************/
    //DB配置文件，数据库配置节点默认第0个节点为主库其他节点为从库
    //不支持多主库
    
    'Dbtype' => 'mypdo',//mysql/mysqli/pdo 
    'Db' => array(
        array(
            'dbhost' => '127.0.0.1',
            'dbport' => '3306',
            'dbname' => 'live',
            'dbuser' => 'live',
            'dbpassword' => '1234live!@#$',
            'unix_socket' => '/data/mysql3306/mysql3306.sock',
        ),
        array(
            'dbhost' => '127.0.0.1',
            'dbport' => '3307',
            'dbname' => 'live',
            'dbuser' => 'live',
            'dbpassword' => '1234live!@#$',
            'unix_socket' => '/data/mysql3307/mysql3307.sock',
        ), 
        /*array(
            'dbhost' => '127.0.0.1',
            'dbport' => '3307',
            'dbname' => 'live',
            'dbuser' => 'live',
            'dbpassword' => '1234live!@#$',
            'unix_socket' => '/data/mysql3307/mysql3307.sock',
        ),*/
    ),
    //redis配置文件，数据库配置节点默认第0个节点为主库其他节点为从库
    //不支持多主库
    'RedisPrefix' => 'live', 
    'Redis' => array(
        array(
            'ip' => "127.0.0.1",
            'port' => '6379',
            'timeout' => 5,
            'auth' => '1234live!@#$',
        ),
        array(
            'ip' => "127.0.0.1",
            'port' => '6380',
            'timeout' => 5,
            'auth' => '1234live!@#$',
        ),
    ),
    //memcache配置
    'MemcachePrefix' => 'live',
    'Memcache' => array(
        array(
            '127.0.0.1',
            '11211',
        ),
        array(
            '127.0.0.1',
            '11212',
        ),
    ),
    //流
    'stream' => array(
        'hls' => 'http://47.93.50.123:8080/hls/',
        'rtmp' => 'rtmp://47.93.50.123/',
        'stream' => 'stream',
    ),
    
    
    
    
    
    
    
    
);