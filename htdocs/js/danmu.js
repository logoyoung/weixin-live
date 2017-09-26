!(function(){
	//注册私有属性
	var danmu = {};
	//
	var _color = ["white"]//"white","Olive","OliveDrab","Orange","OrangeRed","Orchid"];
	//
	var _fontSize = 'bold 12px Arial';
	//
	var _speed = -2;
	//
	var _msg = [];
	//
	var _width = 0;
	//
	var _maxwidth = 200;
	//
	var _height = 0;
	//
	var _ctx = null;
	//
	var _interval = null;
	//
	var robot = ['666','老司机求带','大神啊','二月红前来求药','三月紫前来求婚','二月红前来求药','三月紫前来求婚','什么？大清亡了？','现在的年轻人就不提了','求BGM','请收下我的膝盖','哈哈哈哈啊哈哈','速度速度',];
	//
	var init = function(selector){
		if(typeof selector !=='string' || selector=='')
			return false;
		var canvas = document.getElementById(selector).getElementsByTagName('canvas')[0];
		if(!canvas || typeof canvas.getContext !== 'function')
			return false;
		_ctx = canvas.getContext('2d');
		_width = canvas.clientWidth;
		_height = canvas.clientHeight;
		_draw();
	}
	//
	var _draw_execute = function(){
		_ctx.clearRect(0, 0, _width, _height);
		_ctx.save();
		for (var i = 0;i<_msg.length;i++){
			//var msg = _msg[i];
			if(_msg[i].x < (-_maxwidth)){
				_msg.shift();
				continue ;
			}	
			//console.log(_msg[i]);
			_ctx.fillStyle = _msg[i].color;
			_msg[i].x += _speed;
			_ctx.font = _fontSize;
			_ctx.fillText(_msg[i].content, _msg[i].x, _msg[i].y); 
			_ctx.restore();
		}
	}
	//
	var _draw = function(){
		if(_interval) return false;
		_interval = setInterval(_draw_execute,50);
	}
	//
	var stop = function(){
		if(_interval)
			clearInterval(_interval);
		_interval = null;
		_ctx = clearRect(0, 0, _width, _height);
		_ctx.save();
		_msg = [];
	}
	//
	var push = function(msgstr,setcolor){
		if(typeof msgstr !=='string' || msgstr=='')
			return false;
		var msg = {
			content:msgstr,
			color:(function(){
				if(setcolor)
					return setcolor;
				var colorCount = _color.length;
				var colorI = Math.floor(Math.random()*colorCount);
				return _color[colorI];
			}()),
			x:(function(){
				return _width;
			}()),
			y:(function(){
				var yy = (Math.floor(Math.random()*(130))+20);
				return parseInt(yy/15)*15;
				//return positiony>(_height/2)?positiony-100:positiony+100;
			}()),
		}
		
		_msg.push(msg);
	}
	danmu = {
		send:push,
		stop:stop,
		init:init,
	}
	window.danmu = danmu;
	setInterval(function(){
		var index = Math.floor(Math.random()*robot.length);
		danmu.send(robot[index])},1000);
}())