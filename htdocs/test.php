<?php


do{
    echo "22\n";
}while ($try-- > 0);
exit;

$a = array('liverecord-Y-656173-1996686--20170725133217.flv'
	,'liverecord-Y-656173-7297813--20170725133240.flv');
$b = array_shift($b);
var_dump($a);
var_dump($b);
exit;
/*
namespace Test;
spl_autoload_register(function ($class){
    var_dump($class);
    throw new \Exception('unable to load');
});
try {
    new \Test\Test();
}catch (\Exception $e){
    echo $e;
}
echo "\nend\n";
exit;
/*











$name = strstr('sdsd\\s', '\\', true);
echo $name;exit;

$arr = array(
    ':0' => 'a',
    ':1' => 'b'
);
$t = array_map(function ($v)
{
    return $v;
}, $arr);
var_dump($t);