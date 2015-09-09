$(function(){

function MakeSelectOptionsById(data){
	var html = '';
	if(data){
		if(data.length >0){
			for(var i in data){
				html += '<option value="' + i +'">' + data[i] + '</option>';
			}
		}	
	}
	
	return html;
}

function MakeSelectOptionsByName(data){
	var html = '';
	if(data){
		if(data.length >0){
			for(var i in data){
				html += '<option value="' + data[i].id +'">' + data[i].name + '</option>';
			}
		}	
	}
	
	return html;
}

function NewsCategoryInit(dom){
	
	$.ajax({
            type : 'GET',
            url : '/paper/news/newscateinit',
            dataType : 'json',
			cache: true,
            success: function(rdata) {				
				if(rdata.code == 1){
					
					if(rdata.data.category){
						
						var label_data = rdata.data.label;

						var category = MakeSelectOptionsById(rdata.data.category);

						$("#category_type").empty().html(category).change(function() {
							var value = $(this).val();
							var sec_val = label_data[value];
						
							$("#label_type").empty().html(MakeSelectOptionsByName(sec_val));
						});

						$("#category_type").change();
					}
				}try{
					GetCategoryError();
				}catch(err){						
				
				}				
            },
            error: function() {
                alert("加载失败！");
            }
    });
}

function KeywordsInit(){
	
	var dom = "window";

	// 品牌自动完成
	var autoComlete = G.ui.autoComplete({
		'target': '#keys',
		'listOnClass': 'autocomplete_status_on',
		'itemActiveClass': 'status_on',
		'itemTPL': '<li><span title="{name}" value="{id}">{name}</span></li>',
		'url': '/paper/news/newskeywords',
		'keyName': 'title',
		'cache': true
	});

	autoComlete.bind("success", function(event, rdata) {
		var json = rdata.response, items = [], item; 
		if (json.code === 1) {
			for (var k in json.data) {
				item = json.data[k];
				item.value = item.name;
				items.push(item);
			}
			rdata.response = items;
			$(dom).find(autoComlete.target).removeData('value');
		}
	});

	autoComlete.bind('enter', function(event, params) {
		
		var input_key = $("#keys").val();
		$("#keywords").append('<span class="keyword" title="点击删除">'+input_key+'</span>');
		
		$("#keys").val('');

		$("#keywords").find("span").click(function(){
			$(this).remove();
		});
		/*var items = autoComlete.items,
		activeIndex = autoComlete.getActiveIndex();

		if(items.length==0 || activeIndex== -1){
			return;
		}*/
	});

	autoComlete.bind('complete', function(event, params) {
		var data = autoComlete.items[params.index].opt.data;
		$("#keywords").append('<span class="keyword" title="点击删除">'+data.name+'</span>');

		$("#keywords").find("span").click(function(){
			$(this).remove();
		});
	});
}


//暴露接口
window.G = window.G || {};
window.G.ui = window.G.ui || {};
window.G.ui.news = window.G.ui.news || {};
window.G.ui.news.CategoryInit = NewsCategoryInit;
window.G.ui.news.KeyWordsInit = KeywordsInit;
});



