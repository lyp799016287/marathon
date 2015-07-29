/*
 * common
 */
;(function(window, $, undefined) {
	 if (typeof window.G == 'undefined') {
        window.G = {};
        window.G.common = {};
	}else {
        if (typeof window.G.common == 'undefined') {
            window.G.common = {};
        }
    }

    var common = window.G.common;
    //模版
    common.tpl = {
        "highlight" :  "<li> \
                            <p class='tit'>{#term_name#}</p>\
                            <p class='nu'>{#term_num#}</p>\
                            <p name='ratio' class=''>{#extra_name#}\
                            <span class='{#term_trend#}_{#arrow_color#}'><i></i>{#percent#}<span class='num' name='in_num'>({#term_pre_num#})</span></span>\
                            </p>\
                        </li>",
		"dataName" : ""
    };

    common.selector_arr = [];

    //模版替换
    common.jsonToTpl = function(json,tpl){
        var ret = tpl.replace(/{#(\w+)#}/g,function(a,b){
            if(json[b] || json[b] == 0){
				return json[b];
            }else{
                return "";
            }
        });
		// hack 表格含日期，周末背景颜色
		var dateMatch = null;
		if(dateMatch = ret.match(/\s*<tr(.*?)>\s*<td(.*?)>\s*(\d{4})(\/|-)(\d{1,2})(\/|-)(\d{1,2})/)){
			var date = new Date(dateMatch[3], dateMatch[5] - 1, dateMatch[7]), day = date.getDay();
			if(day == 0 || day == 6)
				ret = ret.replace(/<td/g, '<td style="background:#fff8d2;font-weight:bold" ');
			}
		return ret;
    };

	//获取url参数
	common.getQuery = function(name,url){
		var u  = arguments[1] || window.location.search,
			reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)"),
			r = u.substr(u.indexOf("\?")+1).match(reg);
		return r != null ? r[2] : "";
	}

	//读取cookie
	common.getCookie = function(name) {
		var reg = new RegExp("(^| )" + name + "(?:=([^;]*))?(;|$)"),
			val = document.cookie.match(reg);
		return val ? (val[2] ? unescape(val[2]) : "") : null;
	};

	//将json串组装成为url并返回
    common.jsonToUrl = function(data){
        var arr = [];
        for( var k in data) {
            arr.push(k + "=" + data[k]);
        };
        return arr.join("&");
    };

    //获取选中项value
    common.getOptionValue = function(oDiv){
        var value = ""
        $(oDiv).find("label").each(function(i,c){
            if($(c).parent().hasClass("status_on")){
                value = $(c).attr("value");
                return;
            }
        })
        return value;
    };

	//获取checkbox选中的value值,以逗号分隔
	common.getCheckboxValue = function (oDiv) {
		var v_buffer = [];
		var t_buffer = [];
		$(oDiv).find("input:checkbox").each(function () {
			if ($(this).attr("checked")) {
				if ("-999999" != $(this).val() && "0" != $(this).val()) {
					v_buffer.push($(this).val());
					t_buffer.push($(this).next().text());
				}
			}
		});
		var result = {
			"val" : v_buffer.join(","),
			"text" : t_buffer.join(",")
		}
		return result;
	};

    //初始化时间控件
    common.initDateType = function(){
        if($("#daily_div")){
            $("#start_date_daily").ligerDateEditor({format: "yyyy-MM-dd", showTime: true, initValue:getLast7Days()});
            $("#end_date_daily").ligerDateEditor({format: "yyyy-MM-dd", showTime: true, initValue:yesterday});
        }
        if($("#weekly_div")){
            $("#start_date_weekly").WeekDateEditor({format: "yyyy-MM-dd", showTime: true, initValue:getLast12Weeks()});
            $("#end_date_weekly").WeekDateEditor({format: "yyyy-MM-dd", showTime: true, initValue:lastMonday});
        }
        if($("#monthly_div")){
            $("#start_date_monthly").MonthDateEditor({format: "yyyy-MM-dd", showTime: true, initValue:(getLast12Months()).substring(0, 7)});
            $("#end_date_monthly").MonthDateEditor({format: "yyyy-MM-dd", showTime: true, initValue:lastMonth.substring(0, 7)});
        }
        if($("#quarterly_div")){
            $("#start_date_quarterly").QuarterDateEditor({format: "yyyy-MM-dd", showTime: true, initValue:getLast4Quarters().substring(0, 7)});
            $("#end_date_quarterly").QuarterDateEditor({format: "yyyy-MM-dd", showTime: true, initValue:lastQuarter.substring(0, 7)});
        }
        if($("#yearly_div")){
            $("#start_date_yearly").YearDateEditor({format: "yyyy-MM-dd", showTime: true, initValue:last1Years.substring(0, 4)});
            $("#end_date_yearly").YearDateEditor({format: "yyyy-MM-dd", showTime: true, initValue:lastYear.substring(0, 4)});
        }
    };

	//格式化数据格式
	common.formatRate = function(row, index, value) {
        return common.formatNumber(value, 2) + '%';
    };
    common.formatDecimal = function(row, index, value) {
        return common.formatNumber(value, 2);
    };
    common.formatInt = function(row, index, value) {
        var data = common.formatNumber(value, 0);
        return data ? data.replace(/\.\d\d/, '') : "";
    };

    common.formatNumber = function(num, precision) {
		if(!isNaN(num)){
			var s = parseFloat(num).toFixed(precision),
                sign = s < 0 ? '-' : '';

			s = Math.abs(s);
			s = s.toString().replace(/^(\d*)$/, "$1.");
			s = (s + "00").replace(/(\d*\.\d\d)\d*/, "$1");
			s = s.replace(".", ",");

			var re = /(\d)(\d{3},)/;
			while (re.test(s)) {
				s = s.replace(re, "$1,$2");
			}

			s = s.replace(/,(\d\d)$/, ".$1");

			return sign + s;
		}
		return num;
    };

    common.setLabelCss = function(oDiv, selector){
    	var selector = selector || 'a';

        if($(oDiv).length > 0){
            $(oDiv).find(selector).click(function(){
                $(oDiv).find(selector).removeClass('status_on');
                $(this).addClass('status_on');
                var text = $(this).children().text();
                common.setSelector(oDiv,text);
            });
        }
        $(oDiv).find(selector).eq(0).addClass('status_on');
        $(oDiv).length > 0 && common.setSelector(oDiv, $(oDiv).find(selector).eq(0).children().text());
    };

    //选中项显示
    common.setSelector = function(oDiv, text){
        var id = $(oDiv).attr("id");
        if("全部" == text){
            var show = "none";
        }else{
            var show = "inline-block"
        }
        if($.inArray(id, $.unique(common.selector_arr)) > -1){
            var e = $("#" + id + "_selector");
            if($(oDiv).is(":visible")){
                e.html(text);
                e.css("display", show);
            }else{
                e.remove();
                common.selector_arr.splice($.inArray(id,common.selector_arr),1);
            }
        }else{
            common.selector_arr.push(id);
            var html = "<a href='#' style='display:" + show + ";' class='selected_item' id='" + id  + "_selector'>" + text + "</a>";
            $(html).insertBefore($(".reload"));
        }

        $("i[name='selector_close']").click(function(){
            $(this).parent().css("display", "none");
        });
    };

    //获取url全部参数
    common.getUrlParams = function (url) {
        var url = arguments[0] || window.location.search;
        var params = {};
        var reg = new RegExp("(^|&|#)(\\w+)=([^&|^#]*)", "g");
        var r = url.substr(url.indexOf("\?")+1).match(reg);
        for(var i in r){
			if(parseInt(i)!=0 && !parseInt(i)){
				continue ;
			}
            var temp = r[i].replace(/&|#/, '').match(/(.+)=(.+)/);
            if(temp[1]){
                params[temp[1]] = decodeURI(decodeURI(temp[2]));
            }

        }
        return params ;
    }

    /*
    * 全选功能
    * oDiv, 包括checkbox的容器jquery对象
    * obj.all 是否默认全选，false或true，默认值true
    * obj.visible 不可见的checkbox是否需要选中，默认不可见的元素不选中(false), 传入值为false或true
    * */
    common.selectAll = function(oDiv, obj){
        if(arguments.length <= 1){
            var all = true;
            var visible = false;
        }else{
            if(typeof obj == "object"){
                var all = obj.all !== false? true : false;
                var visible = obj.visible || false;
            }else{
                var all = arguments[1];
                var visible = false;
            }
        }
        oDiv.find('input:checkbox').click(function() {
            var firstId = oDiv.find('input:checkbox:first').attr('id');
            if (firstId == $(this).attr('id')) {
                if(!visible){
                    oDiv.find('input:checkbox').not('#'+firstId).each(function(){
                        if($(this).is(":visible")){
                            $(this).attr('checked', oDiv.find('#'+firstId).attr('checked'));
                        }
                    });
                }else{
                    oDiv.find('input:checkbox').not('#'+firstId).each(function(){
                        $(this).attr('checked', oDiv.find('#'+firstId).attr('checked'));
                    });
                }
            }
            else {
                var length = oDiv.find('input:checkbox:checked').not('#'+firstId).length;
                oDiv.find('input:checkbox:first').attr('checked', length == (oDiv.find('input:checkbox').length - 1));
            }
        });
        if(all){
            if(!visible){
                oDiv.find('input:checkbox').each(function(){
                    if($(this).is(":visible")){
                        $(this).attr('checked', true);
                    }
                });
            }else{
                oDiv.find('input:checkbox').each(function(){
                    $(this).attr('checked', true);
                });
            }
        }
    };

	common.getDate = function(now) {
		now = now ? now : new Date();
		var year = now.getFullYear();       //年
		var month = now.getMonth() + 1;     //月
		var day = now.getDate();            //日

		if (month < 10) {
			month = "0" + month;
		}
		if(day < 10) {
			day = "0" + day;
		}

		return [year, month, day].join('-');
	};

	common.compareDate = function(startDate, endDate) {
		if (!(typeof startDate == 'object' && /Date/.test(startDate.constructor) && typeof endDate == 'object' && /Date/.test(endDate.constructor))) {
			startDate = startDate.split('-');
			startDate = new Date(startDate[0], startDate[1] - 1, startDate[2]);

			endDate = endDate.split('-');
			endDate = new Date(endDate[0], endDate[1] - 1, endDate[2]);
		}

		var diff = startDate - endDate;
		return diff > 0 ? 1 : (diff == 0 ? 0 : -1);
	};

	common.GetDateDiff = function (startTime, endTime, diffType) {
		//将xxxx-xx-xx的时间格式，转换为 xxxx/xx/xx的格式
		startTime = startTime.replace(/\-/g, "/");
		endTime = endTime.replace(/\-/g, "/");

		//将计算间隔类性字符转换为小写
		diffType = diffType.toLowerCase();
		var sTime = new Date(startTime);      //开始时间
		var eTime = new Date(endTime);  //结束时间
		//作为除数的数字
		var divNum = 1;
		switch (diffType) {
			case "second":
				divNum = 1000;
				break;
			case "minute":
				divNum = 1000 * 60;
				break;
			case "hour":
				divNum = 1000 * 3600;
				break;
			case "day":
				divNum = 1000 * 3600 * 24;
				break;
			default:
				break;
		}
		return parseInt((eTime.getTime() - sTime.getTime()) / parseInt(divNum));
	};
	
	//排序
	common.listSortBy = function (arr, field, order){ 
		var refer = [], result=[], order = (order? order : 'asc'), index; 
		
		for(var i in arr){
			refer[i] = arr[i][field]+':'+i;
		}
		refer.sort(); 
		if(order=='desc') refer.reverse(); 
		for(i=0;i<refer.length;i++){ 
			index = refer[i].split(':')[1]; 
			result[i] = arr[index]; 
		} 
		return result; 
	}
})(window, jQuery);

//设置基本选项被选中时的样式，默认选择第一个
function setDivCss(oDiv){
    if($(oDiv).length > 0){
        if($.browser.msie){
            $(oDiv).find('a').die().live("click",function (e) {
                $(oDiv).find('label').removeClass('selected').parent().removeClass('status_on');
                $(oDiv).find('input:radio').attr('checked','');

                var e = e.target || e.srcElement;
                if("A" == e.tagName){
                    $(this).addClass('status_on').next().addClass('selected');
                    $(this).find('input:radio').attr('checked', 'checked');
                }else{
                    $(this).addClass('status_on').next().addClass('selected');
                    if("LABEL" == e.tagName){
                        e.previousSibling.setAttribute('checked', 'checked');
                    }else{
                        $(this).attr('checked', 'checked');
                    }
                }
                return false
            });
        }else{
            $(oDiv).find('input:radio').click(function(event) {
                $(oDiv).find('label').removeClass('selected').parent().removeClass('status_on');
                $(this).next().addClass('selected').parent().addClass('status_on');
                event.stopPropagation();
            });
            $(oDiv).find('a').die().live("click",function () {
                $(oDiv).find('label').removeClass('selected').parent().removeClass('status_on');
                $(this).addClass('status_on').next().addClass('selected');
                $(oDiv).find('input:radio').attr('checked', '');
                $(this).find('input:radio').attr('checked', 'checked');
            });
        }
        $(oDiv).find('input:radio').eq(0).attr('checked', 'checked').next().addClass('selected').parent().addClass('status_on');
    }
};



//定义时间变量
var today = new Date(),
	last7Days = yesterday = window.G.common.getDate(new Date(today - 86400000)),
	last12Weeks = lastMonday = window.G.common.getDate(new Date(today - (today.getDay() + 6) * 86400000)),
	last12Months = lastMonth = window.G.common.getDate(new Date(today.getFullYear(), today.getMonth() - 1, 1)),
	last4Quarters = lastQuarter = getLastQuarter();
	last1Years = lastYear = window.G.common.getDate(new Date(today.getFullYear() - 1, 0, 1));

function getLast7Days() {
	last7Days = yesterday.split('-');
	last7Days = new Date(last7Days[0], last7Days[1] - 1, last7Days[2]);
	last7Days.setDate(last7Days.getDate() - 6);
	last7Days = window.G.common.getDate(last7Days);
	return last7Days;
};

function getLast3Days() {
	last3Days = yesterday.split('-');
	last3Days = new Date(last3Days[0], last3Days[1] - 1, last3Days[2]);
	last3Days.setDate(last3Days.getDate() - 2);
	last3Days = window.G.common.getDate(last3Days);
	return last3Days;
};

function getLast12Weeks() {
	last12Weeks = lastMonday.split('-');
	last12Weeks = new Date(last12Weeks[0], last12Weeks[1] - 1, last12Weeks[2]);
	last12Weeks.setDate(last12Weeks.getDate() - 84);
	last12Weeks = window.G.common.getDate(last12Weeks);
	return last12Weeks;
};

function getLast12Months() {
	last12Months = lastMonth.split('-');
	last12Months = new Date(last12Months[0], last12Months[1] - 1, last12Months[2]);
	last12Months.setMonth(last12Months.getMonth() - 11);
	last12Months.setDate(1);
	last12Months = window.G.common.getDate(last12Months);
	return last12Months;
};

function getLastQuarter() {
	var now = new Date();
	var year = now.getFullYear();       //年
	var month = now.getMonth() + 1;     //月
	var day = now.getDate();            //日

	if (month >=1 && month <=3) {
	   year = year - 1;
	   month = 10;
	}
	else {
	   month = month - 3;
	}

	return window.G.common.getDate(new Date(year, month - 1, 1));
};


function getLast4Quarters() {
	last4Quarters = lastQuarter.split('-');
	last4Quarters = new Date(last4Quarters[0], last4Quarters[1] - 1, last4Quarters[2]);
	last4Quarters.setYear(last4Quarters.getFullYear() - 1);
	last4Quarters.setDate(1);

	last4Quarters = window.G.common.getDate(last4Quarters);
	return last4Quarters;
};
