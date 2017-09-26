<?php
namespace Lib;



class Timer {
    private $startTime;
    private $endTime;
    
    public function __construct(){
        $this->startTime = 0;
        $this->endTime = 0;
    }
    public function start(){
        $this->endTime = 0;
        return $this->startTime = microtime(true);
    }
    public function end(){
        return $this->endTime = microtime(true);
    }
    public function getTime($deci = 4){
        if($this->startTime >= $this->endTime)
            return 0;
        return round($this->endTime-$this->startTime,$deci);
    }
}