// QQ表情插件
(function($){  
	$.fn.qqFace = function(options){
		var defaults = {
			id : 'facebox',
			path : 'face/',
			assign : 'content',
			tip : 'em_'
		};
		var option = $.extend(defaults, options);
		var assign = $('#'+option.assign);
		var id = option.id;
		var path = option.path;
		var tip = option.tip;
		var emData = {"em_1":"[可爱]","em_2":"[笑脸]","em_3":"[囧]","em_4":"[生气]","em_5":"[鬼脸]","em_6":"[花心]","em_7":"[害怕]","em_8":"[我汗]","em_9":"[尴尬]","em_10":"[哼哼]","em_11":"[忧郁]","em_12":"[呲牙]","em_13":"[媚眼]","em_14":"[累]","em_15":"[苦逼]","em_16":"[瞌睡]","em_17":"[哎呀]","em_18":"[刺瞎]","em_19":"[哭]","em_20":"[激动]","em_21":"[难过]","em_22":"[害羞]","em_23":"[高兴]","em_24":"[愤怒]","em_25":"[亲]","em_26":"[飞吻]","em_27":"[得意]","em_28":"[惊恐]","em_29":"[口罩]","em_30":"[惊讶]","em_31":"[委屈]","em_32":"[生病]","em_33":"[红心]","em_34":"[心碎]","em_35":"[玫瑰]","em_36":"[花]","em_37":"[外星人]","em_38":"[金牛座]","em_39":"[双子座]","em_40":"[巨蟹座]","em_41":"[狮子座]","em_42":"[处女座]","em_43":"[天平座]","em_44":"[天蝎座]","em_45":"[射手座]","em_46":"[摩羯座]","em_47":"[水瓶座]","em_48":"[白羊座]","em_49":"[双鱼座]","em_50":"[星座]","em_51":"[男孩]","em_52":"[女孩]","em_53":"[嘴唇]","em_54":"[爸爸]","em_55":"[妈妈]","em_56":"[衣服]","em_57":"[皮鞋]","em_58":"[照相]","em_59":"[电话]","em_60":"[石头]","em_61":"[胜利]","em_62":"[禁止]","em_63":"[滑雪]","em_64":"[高尔夫]","em_65":"[网球]","em_66":"[棒球]","em_67":"[冲浪]","em_68":"[足球]","em_69":"[小鱼]","em_70":"[问号]","em_71":"[叹号]","em_179":"[顶]","em_180":"[写字]","em_181":"[衬衫]","em_182":"[小花]","em_183":"[郁金香]","em_184":"[向日葵]","em_185":"[鲜花]","em_186":"[椰树]","em_187":"[仙人掌]","em_188":"[气球]","em_189":"[炸弹]","em_190":"[喝彩]","em_191":"[剪子]","em_192":"[蝴蝶结]","em_193":"[机密]","em_194":"[铃声]","em_195":"[女帽]","em_196":"[裙子]","em_197":"[理发店]","em_198":"[和服]","em_199":"[比基尼]","em_200":"[拎包]","em_201":"[拍摄]","em_202":"[铃铛]","em_203":"[音乐]","em_204":"[心星]","em_205":"[粉心]","em_206":"[丘比特]","em_207":"[吹气]","em_208":"[口水]","em_209":"[对]","em_210":"[错]","em_211":"[绿茶]","em_212":"[面包]","em_213":"[面条]","em_214":"[咖喱饭]","em_215":"[饭团]","em_216":"[麻辣烫]","em_217":"[寿司]","em_218":"[苹果]","em_219":"[橙子]","em_220":"[草莓]","em_221":"[西瓜]","em_222":"[柿子]","em_223":"[眼睛]","em_224":"[好的]"};
		
		if(assign.length<=0){
			alert('缺少表情赋值对象。');
			return false;
		}
		
		$(this).click(function(e){
			var strFace, labFace;
			if($('#'+id).length<=0){
				strFace = '<div id="'+id+'" style="position:absolute;display:none;z-index:1000;" class="qqFace">' +
							  '<table border="0" cellspacing="0" cellpadding="0"><tr>';
				for(var i=1; i<=71; i++){
					//labFace = '['+tip+i+']';
					faceIndex = tip+i;
					labFace = emData[faceIndex];
					strFace += '<td><img src="'+path+'emoji_'+i+'.png" onclick="$(\'#'+option.assign+'\').setCaret();$(\'#'+option.assign+'\').insertAtCaret(\'' + labFace + '\');" /></td>';
					if( i % 15 == 0 ) strFace += '</tr><tr>';
				}
				strFace += "<td><img onclick=\"$('#saytext').setCaret();$('#saytext').insertAtCaret('[顶]');\" src=\"/res/emoji/emoji_179.png\"></td>";
				strFace += "<td><img onclick=\"$('#saytext').setCaret();$('#saytext').insertAtCaret('[写字]');\" src=\"/res/emoji/emoji_180.png\"></td>";
				strFace += "<td><img onclick=\"$('#saytext').setCaret();$('#saytext').insertAtCaret('[衬衫]');\" src=\"/res/emoji/emoji_181.png\"></td>";
				strFace += "<td><img onclick=\"$('#saytext').setCaret();$('#saytext').insertAtCaret('[小花]');\" src=\"/res/emoji/emoji_182.png\"></td>";
				strFace += '</tr><tr>';
				for(var j=1; j<=42; j++){
					var internal = 182;
					var i = internal + j;
					faceIndex = tip+i;
					labFace = emData[faceIndex];
					strFace += '<td><img src="'+path+'emoji_'+i+'.png" onclick="$(\'#'+option.assign+'\').setCaret();$(\'#'+option.assign+'\').insertAtCaret(\'' + labFace + '\');" /></td>';
					if( j % 15 == 0 ) strFace += '</tr><tr>';
				}
				strFace += '</tr></table></div>';
			}
			$(this).parent().append(strFace);
			var offset = $(this).position();
			var top = offset.top + $(this).outerHeight();
			$('#'+id).css('top',top);
			$('#'+id).css('left',offset.left);
			$('#'+id).show();
			e.stopPropagation();
		});

		$(document).click(function(){
			$('#'+id).hide();
			$('#'+id).remove();
		});
	};

})(jQuery);

jQuery.extend({ 
unselectContents: function(){ 
	if(window.getSelection) 
		window.getSelection().removeAllRanges(); 
	else if(document.selection) 
		document.selection.empty(); 
	} 
}); 
jQuery.fn.extend({ 
	selectContents: function(){ 
		$(this).each(function(i){ 
			var node = this; 
			var selection, range, doc, win; 
			if ((doc = node.ownerDocument) && (win = doc.defaultView) && typeof win.getSelection != 'undefined' && typeof doc.createRange != 'undefined' && (selection = window.getSelection()) && typeof selection.removeAllRanges != 'undefined'){ 
				range = doc.createRange(); 
				range.selectNode(node); 
				if(i == 0){ 
					selection.removeAllRanges(); 
				} 
				selection.addRange(range); 
			} else if (document.body && typeof document.body.createTextRange != 'undefined' && (range = document.body.createTextRange())){ 
				range.moveToElementText(node); 
				range.select(); 
			} 
		}); 
	}, 

	setCaret: function(){ 
		if(!/msie/.test(navigator.userAgent.toLowerCase())) return; 
		var initSetCaret = function(){ 
			var textObj = $(this).get(0); 
			textObj.caretPos = document.selection.createRange().duplicate(); 
		}; 
		$(this).click(initSetCaret).select(initSetCaret).keyup(initSetCaret); 
	}, 

	insertAtCaret: function(textFeildValue){ 
		var textObj = $(this).get(0); 
		if(document.all && textObj.createTextRange && textObj.caretPos){ 
			var caretPos=textObj.caretPos; 
			caretPos.text = caretPos.text.charAt(caretPos.text.length-1) == '' ? 
			textFeildValue+'' : textFeildValue; 
		} else if(textObj.setSelectionRange){ 
			var rangeStart=textObj.selectionStart; 
			var rangeEnd=textObj.selectionEnd; 
			var tempStr1=textObj.value.substring(0,rangeStart); 
			var tempStr2=textObj.value.substring(rangeEnd); 
			textObj.value=tempStr1+textFeildValue+tempStr2; 
			textObj.focus(); 
			var len=textFeildValue.length; 
			textObj.setSelectionRange(rangeStart+len,rangeStart+len); 
			textObj.blur(); 
		}else{ 
			textObj.value+=textFeildValue; 
		} 
	} 
});