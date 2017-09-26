<?php
namespace Controller;

use Lib\Controller;

class Weixin extends Controller{
    public function index(){
        include (ROOT . "weixin/index.php");
    }
}