<?php
namespace Lib;



class Epage {
    static $nocontrol = "";
    static $noaction = "";
    public static function E($msg = ''){
        echo "$msg";
        exit;
    }
}