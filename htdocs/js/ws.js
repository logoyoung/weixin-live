!(function(){
//websocket message
	var msg;
	var connection = 
	{
		uid:100,
		tid:100,
		content:'connect',
		channel:'room',
		server:'ws://47.93.50.123:4326',
	}

	msg = msg||{
		init:function(wsconnection){
			//绑定
			/*this._tid = tid;
			this._uid = uid;
			this._channel = channel;*/
			//注册websocket
			this._wsconnection = wsconnection;
			this._instance           = this.getInstance();
			this._instance.onopen    = this.connect;
			this._instance.onmessage = this.recive;
			return this;
		},
		connect:function(){
			msg.send.call(msg);
		},
		send:function(data){
			var userConnection = this.getUserConnection(data);
			this._instance.send.call(this._instance,userConnection);
		},
		recive:function(data){
			data = JSON.parse(data.data);
			data = JSON.parse(data);
			var channel = data.channel||'';
			var tid     = data.tid||'';
			var content = data.content||'';
			if(!channel || !tid ||!content) return false;
			data = {
				channel:channel,
				content:content
			};
			if('room'===channel) msg.reciveText.call(msg,data);
			if('gift'===channel) msg.reciveGift.call(msg,data);
		},
		sendGift:function(gift){
			if(typeof gift != 'object') return false;
			if('gift'!=gift.channel) return false;
			this._wsconnection.channel = gift.channel;
			this._wsconnection.content = gift.content;
			this.send.call(this);
		},
		sendText:function(text){
			if(typeof text != 'object') return false;
			if('room'!=text.channel) return false;
			this._wsconnection.channel = text.channel;
			this._wsconnection.content = text.content;
			this.send.call(this);
		},
		reciveGift:function(data){
			if(typeof data != 'object') return false;
			if('gift'!=data.channel) return false;
			var gift = {
				channel:data.channel,
				content:data.content,
			}
			
			/*return new Promise(function(resolve,reject){
				resolve(gift);
			});*/
			if(gift.content == '101')
				GIFT.flower.start();
			if(gift.content == '102')
				GIFT.boom.start();
		},
		reciveText:function(data){
			if(typeof data != 'object') return false;
			if('room'!=data.channel) return false;
			var text = {
				channel:data.channel,
				content:data.content,
			}
			//alert(text.content)
			/*window.recive =  new Promise(function(resolve,reject){
				alert('recive');
				resolve(gift);
			});*/
			danmu.send(text.content);
		},
		getInstance:function(data){
			try{
				var ws = new 
				WebSocket(this._wsconnection.server);
			}catch(e){
				console.error(e.message);
			}
			return ws;	
		},
		getUserConnection:function(data){
			//var args = [].slice.call(arguments,1);
			if(!data)
				return JSON.stringify(this._wsconnection);
			this._wsconnection.content = data.content;
			this._wsconnection.channel = data.channel;
			return JSON.stringify(this._wsconnection);
		}
	};

	window.MSG = msg;


	var Event = Event || {
    _listeners: {},    
    // 添加
    addEvent: function(type, fn) {
        if (typeof this._listeners[type] === "undefined") {
            this._listeners[type] = [];
        }
        if (typeof fn === "function") {
            this._listeners[type].push(fn);
        }    
        return this;
    },
    // 触发
    fireEvent: function(type) {
        var arrayEvent = this._listeners[type];
        if (arrayEvent instanceof Array) {
            for (var i=0, length=arrayEvent.length; i<length; i+=1) {
                if (typeof arrayEvent[i] === "function") {
                    arrayEvent[i]({ type: type });    
                }
            }
        }    
        return this;
    },
    // 删除
    removeEvent: function(type, fn) {
    	var arrayEvent = this._listeners[type];
        if (typeof type === "string" && arrayEvent instanceof Array) {
            if (typeof fn === "function") {
                // 清除当前type类型事件下对应fn方法
                for (var i=0, length=arrayEvent.length; i<length; i+=1){
                    if (arrayEvent[i] === fn){
                        this._listeners[type].splice(i, 1);
                        break;
                    }
                }
            } else {
                // 如果仅仅参数type, 或参数fn邪魔外道，则所有type类型事件清除
                delete this._listeners[type];
            }
        }
        return this;
    }
};
}())