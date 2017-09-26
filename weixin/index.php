<?php
/** 
 * 微信入口
 * @logoyoung
 *   */
    include 'conf/configure.php';
    
    //获取配置参数
    function getWeixinConf(){
        return array(
            'token'         =>TOKEN,
            'encodingaeskey'=>encodingaeskey,
            'appid'         =>appid,
            'appsecret'     =>appsecret
        );
    }
    
    $params = getWeixinConf();
    $wechat = new Wechat($params);
    //验证
    $wechat->valid();
    //mylog('check ok');
   //获取消息类型
   $msgType = $wechat->getRev()->getRevType();
   //消息类型处理
   switch ($msgType){
       case $wechat::MSGTYPE_TEXT:
           //todo
           $msg = $wechat->getRev()->getRevContent();
           //mylog(json_encode($msg));
           //$r = robot($msg);
           $r = '敬请期待欢朋直播微信公众号完善哦';
           $wechat->text($r)->reply();
           break;
       case $wechat::MSGTYPE_VOICE:
           //todo
           break;
       case $wechat::MSGTYPE_IMAGE:
           //todo
           break;
       case $wechat::MSGTYPE_EVENT:
           //todo
           break;
       case $wechat::MSGTYPE_NEWS:
           //$wechat->news(array('0'=>array("Title"=>"LOL", "Description"=>"", "PicUrl"=>"http://c.hiphotos.baidu.com/zhidao/pic/item/63d9f2d3572c11df6302d6b5612762d0f603c251.jpg", "Url" =>"http://lol.tgbus.com/news/")))->reply();
           //todo
           break;
       default:
           //$wechat->text('what are you doing?')->reply();
       
   }
   //$menu = $wechat->getMenu();
   $r = $wechat->createMenu($_MENU['three']);
   if($r) mylog('ok');
  else mylog($wechat->errCode.':'.$wechat->errMsg);
    
    
    
    
    