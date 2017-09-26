<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta content="app-id=123456"name="apple-itunes-app"/>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>欢朋手机APP - 欢朋移动客户端</title>
<meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=no,maximum-scale=1.0">
<link rel="stylesheet" href="http://dev.huanpeng.com/weixin/h5/css/appdown.css">
<script type="text/javascript" src="http://dev.huanpeng.com/main/static/js/jquery-1.9.1.min.js"></script>
<style>
html,body{
    width: 100%;
    min-height:100%; 
}
#down-lead{
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('https://i.ssl.pdim.gs/bb19c7bf634ca9b3a10f1869f5f6962c.png');
    background-size: 100% 100%;
    z-index: 9999;
    display: none;
}
#down-lead a{
    display: block;
    position: absolute;
    width: 42.96875%;
    height: 8.4325%;
    top: 50%;
    left: 32.0312%;
}
</style>
<script type="text/javascript">
        !function(){
            var ua;
            var a = {
            versions: function() {
                var e = navigator.userAgent;
                    //t = navigator.appVersion;
                    return {
                        trident: e.indexOf("Trident") > -1,
                        presto: e.indexOf("Presto") > -1,
                        webKit: e.indexOf("AppleWebKit") > -1,
                        gecko: e.indexOf("Gecko") > -1 && e.indexOf("KHTML") == -1,
                        mobile: !!e.match(/AppleWebKit.*Mobile.*/),
                        ios: !!e.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/),
                        android: e.indexOf("Android") > -1 || e.indexOf("Linux") > -1,
                        iPhone: e.indexOf("iPhone") > -1,
                        iPad: e.indexOf("iPad") > -1,
                        Pad: e.indexOf("Pad") > -1,
                        webApp: e.indexOf("Safari") == -1,
                        weixin: e.indexOf("MicroMessenger") > -1,
                        qq: !!function(){
                            var qq = e.match(/\sQQ/i);
                            qq =  qq!= null ? qq : [''];
                            return qq[0].toLowerCase()==' qq'; 
                        }()
                    }
                } (),
                language: (navigator.browserLanguage || navigator.language).toLowerCase()
            };
            ua = a.versions;
            console.log(navigator.userAgent);
            console.log(ua.qq);
            $(function(){
                if(ua.weixin||ua.qq)
                $('#down-lead').css('display','block');
                $(window).on("orientationchange",function(){
                    if(window.orientation!=0)
                        $('#down-lead').css('height','180%');
                    else
                        $('#down-lead').css('height','100%');
                });
            })
        }()
        

    </script>
</head>
<body > 
	<div class="wrap">
		<div class="top" style="background:url(http://dev.huanpeng.com/pc/background.png)">
			<img style=" width: 157px;height:200px; margin:auto;"
				src="http://dev.huanpeng.com/pc/iPhone.png"
				alt=""
			>
		</div>
	</div>
	<div class="btn" id="btn_box">
		<div class="ele_fc pop_android" style="display: none"> 
			<div class="wx">
				<span> <img
					src="http://staticlive.douyutv.com/common/douyu/client/images/wx_scum.png"
					alt=""
				> <a href="javascript:hidden_pop();"></a>
				</span>
			</div>
			<div class="arrows"></div>
		</div>
		<div class="ele_fc pop_ios" style="display: none">
			<div class="wx">
				<span> <img
					src="http://staticlive.douyutv.com/common/douyu/client/images/pop_ios2.png"
					alt=""
				>
				</span>
			</div>
			<div class="arrows"></div>
		</div>
		
		<div class="row main">
			<a href="#"class="dload"> 
			<span class="btn_txt"> 
			<i class="btn_iocn">
			<img src="http://staticlive.douyutv.com/common/douyu/client/images/client_3.png"alt="">
			</i> 
			<b>欢朋IOS版</b>
			</span> 
			</a>
		</div>
		<div class="row main">
			<a href="http://dev.huanpeng.com/main/a/app/download.php"class="dload"> 
			<span class="btn_txt"> 
			<i class="btn_iocn">
			<img src="http://staticlive.douyutv.com/common/douyu/client/images/client_1.png"alt=""></i>
			<b>欢朋Android版</b>
			</span> 
			</a>
		</div>
		<div class="row main">
			<a href="#" class="dload"> 
			<span class="btn_txt"> <i class="btn_iocn">
			<img src="http://staticlive.douyutv.com/common/douyu/client/images/client_81.png" alt="">
			</i> 
			<b>看手机直播</b>
			</span> 
			</a>
		</div>
	</div>
    <div id="down-lead">
    <a id="tipbtn" onclick="document.getElementById('down-lead').style.display='none';"></a>
</div>
</body>
</html>