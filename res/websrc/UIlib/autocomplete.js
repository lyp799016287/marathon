var G = {}
G.ui = {};

(function(window, $, undefined){
	function autoCompleteItem(opt){
		
		this.opt = $.extend({}, {
			'tpl' : '',
			"itemActiveClass" : '',
			"data" : null,
			"index" : -1,
			"url" : ""
		}, opt || {});
		
		this.init();
	}	
	
	autoCompleteItem.prototype = {
		constructor : autoCompleteItem,
		trigger: function(){
			this.element && this.element.triggerHandler.apply(this.element, Array.prototype.slice.call(arguments, 0, arguments.length));
		},
		bind: function(){
			this.element && this.element.bind.apply(this.element, Array.prototype.slice.call(arguments, 0, arguments.length));
		},
		init: function(){
			var self = this,
				_html = this.opt.tpl.toString().replace(/{([^}]+)}/g, function(a, b){
					return undefined === self.opt.data[b] ? '' : self.opt.data[b] 	
				})
				
			this.element = $(_html);
			
			this.element.hover(function(){self.active()}, function(){self.unActive()}).click(function(){
				self.trigger('item_click', self.opt.data);
				return false;
			});
		},
		active: function(){
			this.element && this.element.addClass(this.opt.itemActiveClass);
			this.mouseIn = true;
			this.trigger('active', this.opt.index);
		},
		unActive: function(){
			this.mouseIn = false;
			this.element && this.element.removeClass(this.opt.itemActiveClass);
			this.trigger('unactive');
		},
		remove: function(){
			this.element.remove();
		},
		isActive: function(){
			return !!this.mouseIn;
		},
		getData: function(){
			return this.opt.data;
		}
		
	}
	function autoComplete(opt){
		
		if( !( this.hasOwnProperty && this instanceof autoComplete )){
			return new autoComplete(opt);
		}
		
		this.opt = $.extend({}, {
			"target" : null,
			"listOnClass" : "",
			"delayTime" : 200,
			"elementClass" : "autocomplete",
			"cache" : true,
			"itemTPL" : "<li>{label}</li>",
			"itemActiveClass" : 'on',
			"params" : "",
			"keyName" : "kw",
			"cache" : true
		}, opt || {});
		
		var target = $(this.opt.target);
		
		if(target.length === 0){
			return null;
		}
		
		this.target = target.eq(0);
		
		this.items = [];
		
		this.init();
	};
	
	autoComplete.prototype = {
		constructor : autoComplete,
		keyCode: {
			ALT: 18,
			BACKSPACE: 8,
			CAPS_LOCK: 20,
			COMMA: 188,
			COMMAND: 91,
			COMMAND_LEFT: 91, // COMMAND
			COMMAND_RIGHT: 93,
			CONTROL: 17,
			DELETE: 46,
			DOWN: 40,
			END: 35,
			ENTER: 13,
			ESCAPE: 27,
			HOME: 36,
			INSERT: 45,
			LEFT: 37,
			MENU: 93, // COMMAND_RIGHT
			NUMPAD_ADD: 107,
			NUMPAD_DECIMAL: 110,
			NUMPAD_DIVIDE: 111,
			NUMPAD_ENTER: 108,
			NUMPAD_MULTIPLY: 106,
			NUMPAD_SUBTRACT: 109,
			PAGE_DOWN: 34,
			PAGE_UP: 33,
			PERIOD: 190,
			RIGHT: 39,
			SHIFT: 16,
			SPACE: 32,
			TAB: 9,
			UP: 38,
			WINDOWS: 91 // COMMAND
		},			
		
		
		resize: function(){

			var target = this.target, targetPos = target.offset();
			
			this.element.css({
			//	"width" : ( parseFloat(target.outerWidth(), 10) || 0 ) - ( parseFloat( element.css("border-left-width"), 10) || 0 )  - ( parseFloat( element.css("border-right-width"), 10) || 0 ) - ( parseFloat( element.css("padding-left"), 10) || 0 ) - ( parseFloat( element.css("padding-right"), 10) || 0 ),
				"left" : ( targetPos.left || 0 ),
				"top" : ( parseFloat(targetPos.top, 10) || 0 ) + ( parseFloat(target.outerHeight(), 10) || 0 ) - ( parseFloat(target.css("border-bottom-width"), 10) || 0 )
			});
		},
		
		init: function(){
			var self = this,
				target = this.target,
				opt = this.opt,
				element = $('<ul class="' + opt.elementClass + '"></ul>');
				
			this.element = element;			
			
			$(window).bind('resize', function(){
				self.resize();
			});
			
			this.resize();
			
		
			this.items = [];
			
			this.target.click(function(){
				if(self.items.length > 0){
					self.show();
				}
			});
			this.target.bind("keydown.autocomplete", function(ev){
				self.bindKeyDown(ev);
			});
			
			this.target.bind("keyup.autocomplete", function(event){
				var key = event.keyCode, 
					keyCode = self.keyCode;
				if( key === keyCode.UP || key === keyCode.DOWN || key === keyCode.ENTER || key === keyCode.NUMPAD_ENTER || key === keyCode.LEFT || key === keyCode.RIGHT  ){	
					return ;
				}
				self.queryDelaySend();
			});
			
			this.target.bind("enter.autocomplete", function(){self.hide()});

			this.target.bind("click.autocomplete", function(){self.hide()});
			
			this.target.bind("blur", function(){
				setTimeout(function(){
					self.hide();
				}, 200);
			});
		},
		
		destory : function(){
			
		},
		
		ajax: function(value){
			var self = this;
			
			self.hide();
			this.term = value;
			
			this.cache = this.cache || {};
			
			if(this.opt.cache && undefined !== self.cache[value]){
				self.requestSuccess(self.cache[value]);
			}
			else{
				var param = {};
				
				if(self.opt.keyName){
					param[self.opt.keyName] = value;
				}
				
				self.xhr && self.xhr.abort();
				self.xhr = $.ajax({
					"url" : self.opt.url,
					"data" : $.extend(param, ($.isFunction(self.opt.params) && self.opt.params()) || {}),
					"async" : true,
					"dataType" : "jsonp",
					"cache" : false,
					"crossDomain" : true,
					"scriptCharset" : "gb2312",
					"success" : function(json){
						if(self.opt.cache){
							self.cache[value] = json;
						}
						
						self.requestSuccess(json);
					}
				});
			}
					
		},
		requestSuccess: function(data){
			var self = this,
				result = {response: data};
			this.trigger("success", result);
			
			this.hide();
			if($.isArray(result.response)){
				for(var i = 0 , len = result.response.length; i < len; i++){
					var _item  = new autoCompleteItem({
						data :  result.response[i],
						tpl: self.opt.itemTPL,
						itemActiveClass : self.opt.itemActiveClass,
						itemTPL : self.opt.itemTPL,
						index : i
					});
					this.bindItem(_item);
					this.element.append(_item.element);
					this.items.push(_item);
				}

				if(result.response.length > 0){
					this.show();
				}
			}
		},
		bindItem: function(item){
			var self = this;

			item.bind('item_click', function(event, data){
				self.target[0].value  = data.value;
				self.trigger('complete', {from: 'click', index : item.opt.index});
				self.hide();
			});
			
			item.bind('active', function(event, index){
				for(var i = 0, len = self.items.length; i < len; i++){
					if( i !== index){
						self.items[i].unActive();
					}
				}
			})
		},
		bindKeyDown: function(event){
			var self = this, keyCode = self.keyCode;

			switch( event.keyCode ) {
			case keyCode.UP:
				if(self.isActive()){
					self.prev();
					event.preventDefault();
				}
				break;
			case keyCode.DOWN:
				if(self.isActive()){
					self.next();
					event.preventDefault();
				}
				break;
			case keyCode.ENTER:
			case keyCode.NUMPAD_ENTER:
				self.trigger("enter");
			case keyCode.ESCAPE:
				self.hide( event );
				break;
			default:
			//	self.queryDelaySend();
				break;
			}

		},
		queryDelaySend: function(){
			var self = this;
			
			clearTimeout(self.delayTimer);
			self.delayTimer = setTimeout(function(){
				var _value =  self.target[0].value;
				if(self.term !==_value){
					if($.trim(_value) !== ""){
						self.ajax(_value);
					}
					else{
						self.term = "";
						self.xhr && self.xhr.abort();
						self.hide();
					}
				}
			}, self.opt.delayTime);
		},
		show: function(){
			this.resize();
			this.activeStatus = true;
			this.element && this.element.addClass(this.opt.listOnClass);
			this.element.appendTo("body");
			this.trigger("show");
		},
		hide : function(){
			this.activeStatus = false;
			this.element && this.element.removeClass(this.opt.listOnClass);
			this.element.detach();
			for(var i = 0, len = this.items.length; i < len; i++){
				this.items[i].remove();
			}
			this.items = [];			
			this.trigger("hide");		
		},
		getActiveIndex: function(){
			var _activeIndex = -1;
			for(var i = 0, len = this.items.length ; i < len; i++){
				if(true === this.items[i].isActive()){
					_activeIndex = i;
					break;
				}
			}
			
			return _activeIndex;
		
		},
		prev: function(){
			var _activeIndex = this.getActiveIndex();
			if( -1 !== _activeIndex){
				this.items[_activeIndex].unActive();
				this.target.val(this.term);
			}
			
			_activeIndex = (_activeIndex === -1 ? this.items.length : _activeIndex ) - 1;
			
			if( _activeIndex > -1 ){
				var _item = this.items[_activeIndex];
				_item.active();
				this.target.val(_item.getData().value);
			}
		},		
		next: function(){
			var _activeIndex = this.getActiveIndex();
			if( -1 !== _activeIndex){
				this.items[_activeIndex].unActive();
				this.target.val(this.term);
			}
			
			if( _activeIndex + 1 < this.items.length){
				var _item = this.items[_activeIndex + 1];
				_item.active();
				this.target.val(_item.getData().value);
			}
		},
		isActive: function(){
			return !!this.activeStatus;
		},
		trigger: function(){
			this.element && this.element.triggerHandler.apply(this.element, Array.prototype.slice.call(arguments, 0, arguments.length));
		},
		bind: function(){
			this.element && this.element.bind.apply(this.element, Array.prototype.slice.call(arguments, 0, arguments.length));
		}
	
	}
	
	G.ui.autoComplete = autoComplete;

})(window, jQuery);