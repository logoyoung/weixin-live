<?php
/**
 * 配置文件
 * 
 * @  */


//微信配置

define('TOKEN', 'hhppddeevv112233');//令牌
//define('encodingaeskey', '4i5poL0TLxqXHcBNk5CYzoOZY0hTIcsS4cQJtE3YFvq');//消息加密密钥
define('encodingaeskey', 'OZh4B0S4kkHXaiaWCROGfmt3fOw9lazutvUlX7zvdGs');//消息加密密钥
//define('appid', 'wx4250844af7248127');//应用ID
define('appid', 'wx4250844af7248127');//应用ID
//define('appsecret', 'd4d0f4ce869342dd2ed3805b5ab118e8');//应用密钥
define('appsecret', 'd48d72c085772f605a335c3cd1b2f742');//应用密钥
//日志配置
define('WEIXIN_LOG', '/data/logs/weixin.log');
//目录配置
define('WEIXIN', ROOT . 'weixin/');
define('SDK', WEIXIN.'sdk/');
define('UTILS',WEIXIN.'utils/');
define('CONF',WEIXIN.'conf/');
//时区
date_default_timezone_set('Asia/Shanghai');

//小黄鸡智能接口
define('robot_key', 'RVdyditvYmxiZzhDPVZXZUFndGFRYis9MkFZQUFBPT0');
define('robot_api', 'http://api.douqq.com/?');
//加载
include ( SDK.'wechat.class.php' );
include ( UTILS.'utils.php' );
include ( CONF.'menu.php' );
include ( UTILS.'myfn.php' );






