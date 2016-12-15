# -*- coding: utf-8 -*-
import os,sys,json
reload(sys)
sys.setdefaultencoding('utf8')
from etc.setting import *
from wechatpy.client.api import WeChatJSAPI
import time,requests
import random
class Util():


	def __init__(self):
		pass


	def __del__(self):
		pass

	def is_valid_date(self,str):
		try:
			datetime.strptime(str, "%Y-%m-%d")
			return True
		except:
			return False

	# 格式化字符串，给数字字符串前加上指定个数的0
	def prezero(self, num, zeronum=2):
		i = int(num)
		s = str(i)
		r = ''
		if len(s)>=zeronum:
			r = s
		else:
			l = zeronum - len(s)
			for x in range(0, l):
				r += '0' + s

		return r

	def get_week_day(self,week_day):
		day_name = ''
		week_day = int(week_day)
		if week_day==1:
			day_name = '周一'
		elif week_day==2:
			day_name = '周二'
		elif week_day==3:
			day_name = '周三'
		elif week_day==4:
			day_name = '周四'
		elif week_day==5:
			day_name = '周五'
		elif week_day==6:
			day_name = '周六'
		elif week_day==7:
			day_name = '周日'
		return day_name

	def get_weekday_of_date(self,datestr):
		_date = datetime.strptime(datestr,'%Y-%m-%d')
		_weekday = _date.weekday()
		return self.get_week_day(_weekday+1)

	def get_weekdaynum_of_date(self,datestr):
		_date = datetime.strptime(datestr,'%Y-%m-%d')
		_weekday = _date.weekday()
		return _weekday

	def get_simple_date(self,datestr):
		_date = datetime.strptime(datestr,'%Y-%m-%d')

		return datetime.strftime(_date,'%m.%d')

	def get_wx_shareinfo(self,url):

	    client = WeChatClient(APPID, APP_SECRET)
	    jsapi = WeChatJSAPI(client)
	    jsticket = jsapi.get_jsapi_ticket()
	    # print '============jsticket============'
	    # print jsticket
	    timestamp = time.time()
	    signature = jsapi.get_jsapi_signature(NONCESTR,jsticket,timestamp,url)
	    # print '==============signature=================='
	    # print signature

	    wx = {'appId':APPID,'timestamp':timestamp,'nonceStr':NONCESTR,'signature':signature,'url':url}

	    return wx

	def get_kms(self,project):
		kms = 0
		if project=='5k':
			kms = 5
		elif project =='10k':
			kms =10
		elif project =='15k':
			kms =15
		elif project =='half_marathon':
			kms =21.095
		elif project =='25k':
			kms =25
		elif project =='marathon':
			kms =42.195

		return kms
	def get_vdot(self,ks,times):
		url = VDOT_API+'?from=app'
		params = {
			'distance':ks,
			'unit':'km',
			'time':times,
			'pace':'empty',
			'punit':'km',
			'wind':'',
			'wunit':'mph',
			'wtype':'head',
			'temp':'',
			'tunit':'F',
			'alt':'',
			'aunit':'ft',
			'advtype':'wind',
			'predict':'true'
		}
		msg=requests.post(url,params).text
		return msg
	def get_show(self,type):
		num=type.split("、")
		reg=[]
		# print '________________________num___________________'
		ex=random.sample(num, 3)
		return ex




