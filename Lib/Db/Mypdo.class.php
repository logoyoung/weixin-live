<?php
/*
 * PDO类
 * 
 */
namespace Lib\Db;
use Lib\Exception;
use Lib\Log;
use Lib\Timer;
use PDOException;
use PDO;


class Mypdo{
    
    //PDO预处理对象
    protected $PDOstatement = null;
    //sql语句
    protected $PDOsql    = '';
    //插入ID
    protected $PDOlastid = null;
    //影响行数
    protected $PDOaffectrows = 0;
    //事务
    protected $PDOtranstimes = 0;
    //错误信息
    protected $PDOerror = '';
    //
    /*protected $PDOwrite = null;
    protected $PDOread = null;*/
    //主库
    protected $PDOmaster = false;
    //当前配置
    protected $PDOconfig = array();
    //重试次数
    protected $try = 1;
    //连接实例
    protected $PDOlinks = array();
    //当前连接
    protected $PDOlink = null;
 
    
    //数据库配置
    protected static $dbConf = array();
    //PDO连接参数
    protected static $PDOoptions = array(
        PDO::ATTR_CASE              =>  PDO::CASE_NATURAL,
        PDO::ATTR_ERRMODE           =>  PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_ORACLE_NULLS      =>  PDO::NULL_NATURAL,
        PDO::ATTR_STRINGIFY_FETCHES =>  false,
    );
    //绑定参数
    protected $bindParams = array();
    
    
    public function __construct($config=''){
        self::$dbConf = !empty($config)&&is_array($config)?$config:$GLOBALS['conf']['Db'];
    }
    
    /**
     * 查询
     * @param unknown $sql
     * @param array $params
     * @param string $debug
     * @param unknown $mode
     * @throws Exception
     * @return unknown
     */
    public function query($sql, array $params = array(), $debug = false, $mode = PDO::FETCH_ASSOC){
        $try = $this->try;
        $timer = new Timer();
        do{
            try {
                $this->connect($this->PDOmaster);
                //不计算连接时间
                $timer->start();
                $this->PDOstatement = $this->PDOlink->prepare($sql);
                $this->bindParams($params);
                $this->PDOstatement->execute();
                $results = $this->PDOstatement->fetchAll($mode);
                $timer->end();
                $time = $timer->getTime();
                Log::write($time);
                $timer = null;
                break;
            }catch(PDOException $e){
                $timer->end();
                $time = $timer->getTime();
                Log::write($time);
                $timer = null;
                $this->free();  
                throw $e; 
            }
            
        }while($try-- > 0);
        return  $results; 
    }
    /**
     * sql执行
     * @param unknown $sql
     * @param array $params
     * @param string $debug
     * @throws Exception
     * @return boolean
     */
    public function execute($sql, array $params = array(), $debug = false){
        $try = $this->try;
        do{
            try {
                $this->connect(true);
                //不计算连接时间
                $timer = new Timer();
                $timer->start();
                $this->PDOstatement = $this->PDOlink->prepare($sql);
                $this->bindParams($params);
                $this->PDOstatement->execute();
                $this->PDOaffectrows = $this->PDOstatement->rowCount();
                $timer->end();
                $time = $timer->getTime();
                Log::write($time);
                $timer = null;
                break;
            }catch(PDOException $e){
                $timer->end();
                $time = $timer->getTime();
                Log::write($time);
                $timer = null;
                $this->free();
                throw $e;
            }
        
        }while($try-- > 0);
        return true;
    }
    /**
     * 开启事务
     * @throws Excetion
     * @return void|boolean
     */
    public function beginTrans(){
        if($this->PDOtranstimes !== 0)
            return ;
        $this->free();
        $this->connect(true);
        if(!$this->PDOlink)
            return false;
        $this->PDOtranstimes++;
        try {
            if($this->transTimes === 0)
                $this->PDOlink->beginTransaction();
        }catch(PDOException $e){
            $this->PDOtranstimes++;
            Log::write();
            throw $e;
        }
    }
    /**
     * 提交事务
     * @throws Excetion
     * @return boolean
     */
    public function commit(){
        if($this->PDOtranstimes <= 0 || empty($this->PDOlink))
            return false;
        $this->PDOtranstimes = 0;
        try {
            $this->PDOlink->commit();
        }catch(PDOException $e){
            Log::write();
            throw $e;
        }
    }
    /**
     * 回滚事务
     * @throws Excetion
     * @return boolean
     */
    public function rollback(){
        if($this->PDOtranstimes <= 0 || empty($this->PDOlink))
            return false;
        $this->PDOtranstimes = 0;
        try {
            $this->PDOlink->rollback();
        }catch(PDOException $e){
            Log::write();
            throw $e;
        }
    }
    /**
     * 获取插入ID
     * @throws Exception
     */
    public function getLastId(){
        if($this->PDOlink instanceof PDO)
            return $this->PDOlink->lastInsertId();
        throw new Exception('pdo connection not exists');
    }
    /**
     * 获取影响行数
     * @return number
     */
    public function getAffectRow(){
        return $this->PDOaffectrows;
    }
    /**
     * 释放连接
     */
    public function free(){
        $this->PDOstatement = null;
        $this->PDOlink = null;
        $this->PDOlinks = array();
    }
    /**
     * 绑定参数
     * @param array $params
     */
    public function bindParams(array $params){
        foreach ($params as $key => &$value){
            $type = is_int($value)?PDO::PARAM_INT:PDO::PARAM_STR;
            $field = is_int($key)?($key+1):':' . $key;
            $this->PDOstatement->bindParam($field,$value,$type);
        }
    }
    /**
     * 连接
     * @param string $master
     * @throws Exception
     */
    public function connect($master = false){
        $config = self::getDbConf($master);
        //方式1:同一个请求落在不同库，不同请求随机分配库，连接消耗多，分担库压力
        //$linkId = md5(serialize($config));
        //方式2:同一个请求落在同一个库，不同请求随机分配库，库压力重，减少连接消耗
        $linkId = $master?0:1;
        if(!empty($this->PDOlinks[$linkId]) && ($this->PDOlinks[$linkId] instanceof PDO)){
            $this->PDOlink = $this->PDOlinks[$linkId];
            return $this->PDOlink;
        }           
        //PDO连接获取dns
        $dns = self::getDns($config);
       try{
           //记录连接时间
           $timer = new Timer();
           $timer->start();
           $this->PDOlinks[$linkId]= new PDO($dns, $config['dbuser'], $config['dbpassword'], self::$PDOoptions);
           $timer->end();
           Log::write($timer->getTime());
          return $this->PDOlink = $this->PDOlinks[$linkId];  
       }catch(PDOException $e){
           Log::write($e);
           throw $e;
       }
    }
    
    /**
     * 获取dns
     * @param string $config
     * @return boolean|string
     */
    public static function getDns($config = ''){
        if(!$config) return false;
        $dsn = 'mysql:dbname=' . $config['dbname'] . ';host=' . $config['dbhost'];
        if(!empty($config['dbport']))
            $dsn .= ';port=' . $config['dbport'];
        if (!empty($config['unix_socket'])) {
            $dsn .= ';unix_socket=' . $config['unix_socket'];
        }
        if(!empty($config['dbcharset']))
            $dsn .= ';charset=' . $config['dbcharset'];
        var_dump($dsn);
        return $dsn;
    }
    /**
     * 获取数据库配置
     * @param string $master
     * @return multitype:
     */
    public static function getDbConf($master = false){
        $dbConf = self::$dbConf;
        if($master)
            return array_change_key_case($dbConf[0], CASE_LOWER);
        else{
            $slave = count($dbConf) - 1;
            $randLinkId = mt_rand(1, $slave);
            return array_change_key_case($dbConf[$randLinkId], CASE_LOWER);
        }
    }
    /**
     * 释放资源
     */
    public function __destruct(){
        $this->free();
    }
}