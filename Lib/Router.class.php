<?php
/**
 * 路由
 */
namespace Lib;

class Router{
    private $namespace;
    private $basePath;
    private $requestUrl;
    
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }
    
    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;
    }
    
    public function getRequestUrl()
    {
        return $this->requestUrl;
    }
    
    public function getMatch($requestUrl = null, $requestMethod = null)
    {
        $match  = false;
    
        /*if($requestUrl === null)
        {
            $requestUrl = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : false;
        }
        
        if($requestUrl === false || $requestUrl === '/' )
        {
            return $match;
        }
        
        $requestUrl = trim($requestUrl,'/');
        $requestUrl = substr($requestUrl, strlen($this->basePath));
        
        if (($strpos = strpos($requestUrl, '?')) !== false)
        {
            $requestUrl = substr($requestUrl, 0, $strpos);
        }*/
        
        if(!CLI){
            if($requestUrl === null)
            {
                $requestUrl = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : false;
            }
        
            if($requestUrl === false || $requestUrl === '/' )
            {
                return $match;
            }
        
            $requestUrl = trim($requestUrl,'/');
            $requestUrl = substr($requestUrl, strlen($this->basePath));
        
            if (($strpos = strpos($requestUrl, '?')) !== false)
            {
                $requestUrl = substr($requestUrl, 0, $strpos);
            }
        
        }else {
            $rule = $GLOBALS['argv'];
            array_shift($rule);
            $requestUrl = $rule[0];
        }
        
        $this->requestUrl = $requestUrl;
        $requestArr = explode('/',$this->requestUrl);
        $match      = array(
            'namespace'  => false,
            'controller' => false,
            'action'     => false,
        );
        
        $match['controller'] = ucfirst(array_shift($requestArr));
        if($requestArr)
        {
            $match['action'] = array_shift($requestArr);
        }
        
        $match['namespace'] = $this->namespace;
        $match['namespace'] = rtrim($match['namespace'], '\\').'\\';
        return $match;
    }
}

