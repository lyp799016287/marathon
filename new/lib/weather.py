#!/usr/bin/python
#coding=utf-8
import requests
from imp import reload
import sys
import json
import chardet
from pypinyin import lazy_pinyin
import pypinyin
import re

if sys.version[0] == '2':
    reload(sys)
    sys.setdefaultencoding("utf-8")

#中文转化为拼音
def pinyin(string):
	result =lazy_pinyin(string,style=pypinyin.NORMAL)
	cityName = ''
	for item in result:
		# print(item)
		cityName = cityName + (str(item))
	return cityName

#判断字符串是否为中文字符
def isHans(contents):
	zhPattern = re.compile(u'[\u4e00-\u9fa5]+')
	#一个小应用，判断一段文本中是否包含简体中：
	match = zhPattern.search(contents)
	if match:
		# print(u'有中文：%s' % (match.group(0),))
		return True
	else:
		# print(u'没有包含中文')
		return False

#获取城市名
def getCityName():
	city = ''
	print(u"请输入要查询的城市:")
	city = input()
	if isHans(city):
		city = pinyin(city)
	return city

# 百度天气API
def getWeather(city=''):
	if city=='':
		cityName = getCityName()
	else:
		cityName = city
	url = 'http://apis.baidu.com/heweather/weather/free?city=' + cityName
	headers = {"apikey": "f8ce490f56adc4656a922dafff80e9d5"}#填入自己的APIkey就行了
	resp = requests.get(url,headers=headers) 
	content = resp.text

	# 查看获取的代码的编码格式
	print(type(content))

	if content:
		print(content.decode('utf-8'))
		with open('weather.json', 'w') as f:
			f.write(content)
			#f.write(content.decode('utf-8'))
	else:
		print(u'保存天气json文件的时候出错啦~~~')

#处理数据
def handleData():
	#读取json数据
	with open('weather.json', 'r') as f:
		weather_data = json.load(f)
		if weather_data:
			data_dict = weather_data["HeWeather data service 3.0"][0]
			# print(data_dict)
			detail_data = {}
			detail_data["cnty"] = data_dict["basic"]["cnty"]
			detail_data["city"] = data_dict["basic"]["city"]
			detail_data['date'] = data_dict["daily_forecast"][0]["date"]
			detail_data["weather"] = '白天'+data_dict["daily_forecast"][0]["cond"]["txt_d"]\
			                     + ',夜间' + data_dict["daily_forecast"][0]["cond"]["txt_n"]
			detail_data['nowtmp'] = data_dict["now"]["tmp"]
			detail_data['maxtmp'] = data_dict["daily_forecast"][0]["tmp"]["max"]
			detail_data['mintmp'] = data_dict["daily_forecast"][0]["tmp"]["min"]
			detail_data['feeltmp'] = data_dict["now"]["fl"]
			if "astro" in data_dict["daily_forecast"][0] and "sr" in data_dict["daily_forecast"][0]["astro"]:
				detail_data['astro'] = '日出时间：'+data_dict["daily_forecast"][0]["astro"]["sr"]+'\n日落时间：'+data_dict["daily_forecast"][0]["astro"]["ss"]
			if "aqi" in data_dict:
				detail_data['qlty'] = data_dict["aqi"]["city"]["qlty"]

			detail_data['sport'] = '暂无'
			if "suggestion" in data_dict:
				if "sport" in data_dict["suggestion"]:
					if "brf" in data_dict["suggestion"]["sport"]:
						detail_data['sport'] = data_dict["suggestion"]["sport"]["brf"]

			with open('weather_forecast.txt', 'w') as f:
				for item in detail_data:
					f.writelines(item.encode('utf-8').decode('utf-8') + \
					':' + detail_data[item].encode('utf-8').decode('utf-8') + '\n')
			return detail_data
			# print(detail_data)
		else:
			print(u'处理json数据的时候出错啦~~~')


#展示数据
def showDataByText():
	data = handleData()
	if data:
		print(data["cnty"])
		print(u"国家：", data["cnty"])
		print(u"城市：", data["city"])
		print(u"时间：", data["date"])
		print(u"天气：", data["weather"])
		if "qlty" in data:	#有的城市没有空气质量这一数据，所以稍作处理
			print(u"空气质量：", data["qlty"])
		else:
			print(u"空气质量：哎呀！这地儿太小了，空气质量查不了~~~")
		print(u"温度：", data["nowtmp"])
		print(u"体感温度：", data["feeltmp"])
		print(u"最高温度：", data["maxtmp"])
		print(u"最低温度：", data["mintmp"])
	else:
		print(u'输出天气情况的时候出错啦~~~')

def getWeatherResult(city):
	getWeather(city)
	data = handleData()
	res = "";
	if data:
		
		res +=data["cnty"]
		res +=','+data["city"]
		res +=','+data["date"]
		res +='\n天气：'+data["weather"]
		if "qlty" in data:
			res +='\n空气质量：'+data["qlty"]
		res +='\n当前温度：'+data["nowtmp"]+"℃"
		res +='\n体感温度：'+data["feeltmp"]+"℃"
		res +='\n温度区间：'+data["mintmp"]+"℃"+'~'+data["maxtmp"]+"℃"
		if "astro" in data:
			res +='\n'+data["astro"]
		res +='\n运动建议：'+data["sport"]
		

		# print(u"国家：", data["cnty"])
		# print(u"城市：", data["city"])
		# print(u"时间：", data["date"])
		# print(u"天气：", data["weather"])
		# if "qlty" in data:	#有的城市没有空气质量这一数据，所以稍作处理
		# 	print(u"空气质量：", data["qlty"])
		# else:
		# 	print(u"空气质量：哎呀！这地儿太小了，空气质量查不了~~~")
		# print(u"温度：", data["nowtmp"])
		# print(u"体感温度：", data["feeltmp"])
		# print(u"最高温度：", data["maxtmp"])
		# print(u"最低温度：", data["mintmp"])
	else:
		res = 'no result'
	return res

# def main():
# 	if __name__ == "__main__":
# 		print getWeatherResult('shanghai')
# 		# check = 'y'
# 		# while(check == 'y'):
# 		# 	getWeather()
# 		# 	handleData()
# 		# 	showDataByText()
# 		# 	check = input("是否继续查询（y/n）:")

# main()
