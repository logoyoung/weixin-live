<?php
/**
 * 数据库操作
 */
namespace Lib;
use Lib\Exception;

class Db{
    //数据裤链接
    private static $_dbs = array();
    //当前链接
    private static $_db  = null;
    
    /**
     * 获取数据库实例
     * @param unknown $dbname
     * @throws Exception
     */
    public static function getDb($dbname = 0){
        $dbkey = md5($dbname);
        if(!isset(self::$_dbs[$dbkey])){
            //获取连接方式
            $dbType = $GLOBALS['conf']['Dbtype'];
            $class = 'Lib\\Db\\' . ucwords(strtolower($dbType));
            if(class_exists($class)){
                self::$_dbs[$dbkey] = new $class();
            }else{
                throw new Exception('class '. $class . ' not found');
            }
        }
        self::$_db = self::$_dbs[$dbkey];
        return  self::$_db;
    }  
    
    
    /**
     * 回调实例方法
     * @param unknown $method
     * @param unknown $args
     * @return mixed
     */
    public static function __callstatic($method,$args){
        return call_user_func_array(array(self::$_db,$method), $args);
    }
    
    /*public static function __call($method,$args){
        return call_user_func_array(array(self::$_db,$method), $args);
    }*/
}