$.extend({  
    getUrlVars: function(){  
      var vars = [], hash;  
      var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');  
      for(var i = 0; i < hashes.length; i++)  
      {  
        hash = hashes[i].split('=');  
        vars.push(hash[0]);  
        vars[hash[0]] = hash[1];  
      }  
      return vars;  
    },  
    getUrlVar: function(name){  
      return $.getUrlVars()[name];  
    }  
});  
$().ready(function(){
	var lefter = $('#left');
	if(lefter.size()>0){
		$(lefter).height($(window).height());
	}
	$(window).resize(function(){
		if(lefter.size()>0){
			$(lefter).height($(window).height());
		}
	});

	if($.cookie('userName')==""||$.cookie('password')==''){return;}
	$.post('/login/login/sessionVarify',function(data){
		if(data.code==1){
			//window.location='/newsList.htm';
			if($('#userName').size()==1){
				$('#userName').text($.cookie('userName'));
			}
			/*if($('#userinfo').size()==1){
				$('#userinfo').append('<a class="logout" href="javascript:void(0);">退出</a>');
				//$('#userinfo').append('<a class="navop" status="1" href="javascript:void(0);">收起</a>');
				$('a.logout').click(function(){
					$.cookie('userName','',{path:'/'});
					$.cookie('password','',{path:'/'});
					window.location='/index.htm';
				});
				$('a.navop').click(function(){
					if($(this).attr('status')==1){
						$(this).attr('status',0);
						$('#left').hide();
						$('#main_body').css('margin-left',0);
					}else{
						$(this).attr('status',1);
						$('#left').show();
						$('#main_body').css('margin-left','15rem');
					}
				});
			}*/
			if($('#menu-right').size()==1){
				$('#menu-right').append('<li><a href="javascript:void(0);" class="logout"><span aria-hidden="true" class="glyphicon glyphicon-off"></span>退出</a></li>');
				//$('#userinfo').append('<a class="navop" status="1" href="javascript:void(0);">收起</a>');
				$('a.logout').click(function(){
					$.cookie('userName','',{path:'/'});
					$.cookie('password','',{path:'/'});
					window.location='/index.htm';
				});
			}
		}else{
			//alert(data.message);
			$.cookie('userName','',{path:'/'});
			$.cookie('password','',{path:'/'});
			if(window.location.href.indexOf('/index.htm')==-1){
				window.location='/index.htm';
			}
			
		}
	});
});