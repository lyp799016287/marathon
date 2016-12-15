# -*- coding: utf-8 -*-
import os,sys,json
reload(sys)
sys.setdefaultencoding('utf8')
import requests
from datetime import datetime,date,time,timedelta

class Lbs():
	"""docstring for ClassName"""
	api_geo = 'http://restapi.amap.com/v3/geocode/regeo'
	api_wether = 'http://restapi.amap.com/v3/weather/weatherInfo'
	api_key = 'e9768e389e65516ed4a11c05c07f40c0'
	weekday = {'1':'周一','2':'周二','3':'周三','4':'周四','5':'周五','6':'周六','7':'周日'}

	def __init__(self):
		pass


	def get_geo(self,longitude,latitude):
		adcode = ''
		params = '?'
		params += 'key='+self.api_key
		params += '&location='+longitude+','+latitude
		url = self.api_geo+params

		# print url

		resp = requests.get(url)
		content = resp.text
		result = json.loads(content)
		# print result
		# print type(content)
		if result['status']=='1':
			try:
				adcode = result['regeocode']['addressComponent']['adcode']
			except Exception as e:
				print e
			else:
				pass
			finally:
				pass



		return adcode

	def get_weather(self,adcode):
		params = '?'
		params += 'key='+self.api_key
		params += '&city='+adcode
		url = self.api_wether+params

		# print url

		resp = requests.get(url)
		content = resp.text
		result = json.loads(content)
		# print result

		ret = ''

		if result['status']=='1':
			try:
				live = result['lives'][0]

				# print '======================='
				# print live
				# print '======================='
				ret+= live['province']+live['city']+'实时天气：'
				ret+='\n天气：'+live['weather']
				ret+='\n温度：'+live['temperature']+'℃'
				ret+='\n'+live['winddirection']+'风： '+live['windpower']+' 级'
				ret+='\n湿度：'+live['humidity']+'%'
				ret+='\n更新于 '+ live['reporttime'][:16]
			except Exception as e:
				print e
			else:
				pass
			finally:
				pass


		return ret

	def get_weather_later(self,adcode):
		params = '?'
		params += 'key='+self.api_key
		params += '&city='+adcode
		params += '&extensions=all'
		url = self.api_wether+params

		# print url

		resp = requests.get(url)
		content = resp.text
		result = json.loads(content)
		# print result

		ret = ''

		if result['status']=='1':
			try:
				forecasts = result['forecasts']
				if len(forecasts)>0:
					for forcast in forecasts:
						if len(forcast)>0:
							# ret+= forcast['province']+forcast['city']+'4日天气预报：'
							ret+= '接下来4天：'
							if forcast['casts']:
								casts = forcast['casts']
								i = 0
								for cast in casts:
									i += 1
									if i==1:
										continue
									# print '======================='
									# print cast
									# print '======================='
									ret+='\n'+cast['date']+' '+self.weekday[cast['week']]
									if cast['dayweather']==[]:
										dayweather = ''
									else:
										dayweather = '☀'+cast['dayweather']+' '

									if cast['daytemp']==[]:
										daytemp = ''
									else:
										daytemp = '☀'+cast['daytemp']+'℃ '

									if cast['daywind']==[]:
										daywind = ''
									else:
										daywind = '☀'+cast['daywind']+cast['daypower']+'级 '


									ret+='\n'+dayweather+''+cast['nightweather']
									ret+='\n'+daytemp+''+cast['nighttemp']+'℃'
									ret+='\n'+daywind+''+cast['nightwind']+'风'+cast['nightpower']+'级'
									ret+='\n'

			except Exception as e:
				print e
			else:
				pass
			finally:
				pass


		return ret

	def get_tomorrow_weather (self, adcode, tomorrow):
		params = '?'
		params += 'key='+self.api_key
		params += '&city='+adcode
		params += '&extensions=all'
		url = self.api_wether+params

		#print url
		#print tomorrow

		resp = requests.get(url)
		content = resp.text
		result = json.loads(content)
		#print result

		ret = ''

		if result['status']=='1':
			foreasts = result['forecasts']
			if foreasts:
				for forcast in foreasts:
					ret += forcast['province'] + ' ' + forcast['city'] + ' ' + tomorrow
					if forcast['casts']:
						for cast in forcast['casts']:
							if str(cast['date']) == tomorrow:
								#print cast
								ret += '\n天气：白天' + cast['dayweather'] + '，夜间'+ cast['nightweather']
								ret += '\n温度区间： ' + cast['nighttemp'] + '°C~'+ cast['daytemp'] + '°C'
								ret += '\n风力： 白天' + cast['daywind'] + '风'+ cast['daypower'] + '级' + '，夜间'+ cast['nightwind'] + '风' + cast['nightpower'] + '级'
								#print ret




		return ret

