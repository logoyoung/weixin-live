<?php
/**
 * 入口文件
 * 
 */
//
//default controller
defined('DEFAULT_MODEL') or define('DEFAULT_MODEL', 'Controller');
defined('DEFAULT_CONTROLLER') or define('DEFAULT_CONTROLLER', 'Index');
defined('DEFAULT_ACTION') or define('DEFAULT_ACTION', 'Index');
require dirname(__DIR__) . '/Lib/core.php';
