<?php
namespace Lib;
use Lib\Exception;
use Lib\Router; 


class Dispatcher
{

    const ERROR_ROUTER_EMPTY     = 650;
    const ERROR_NAMEPSACE_EMPTY  = 651;
    const ERROR_CONTROLLER_EMPTY = 652;
    const ERROR_ACTION_EMPTY     = 653;

    private $namespace;
    private $controller;
    private $action;
    private $match;
    private $router;
    private $params;
    private $callback;

    public function run()
    {   
        //$this->controller = '\\Controller' . $this->controller;
        
        if(!class_exists($this->controller))
        {
            //throw new Exception("controller {$this->match['controller']} not found");
            Epage::E("controller {$this->match['controller']} not found");
        }

        $controller = new $this->controller();
        if(!is_callable(array($controller,$this->action)))
        {
            //throw new Exception("action {$this->action} not found ");
            Epage::E("action {$this->action} not found ");
        }

        if($this->callback)
        {
            foreach ($this->callback as $call)
            {
                call_user_func_array($call, array($this));
            }
        }

        call_user_func_array(array($controller,$this->action), array($this->params));
    }

    public  function setRouter(Router $router)
    {

        $match  = $router->getMatch();
        $this->router = $router;
       
        if($match === false )
        {
            throw new Exception('empty router', self::ERROR_ROUTER_EMPTY);
        }

        $this->match = $match;

        if(!isset($this->match['namespace']) && !$this->match['namespace'])
        {
            throw new Exception('empty namespace', self::ERROR_NAMEPSACE_EMPTY);
        }

        $this->namespace = $this->match['namespace'];

        if(!isset($this->match['controller']) && !$this->match['controller'])
        {
            throw new Exception('empty controller', self::ERROR_CONTROLLER_EMPTY);
        }

        $this->controller = $this->namespace.$this->match['controller'];

        if(!isset($this->match['action']) && !$this->match['action'])
        {
            throw new Exception('empty action', self::ERROR_ACTION_EMPTY);
        }

        $this->action = $this->match['action'];

        return $this;
    }

    public function setCallback($callback)
    {
        $this->callback = $callback;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function getController()
    {
        return $this->controller;
    }

}