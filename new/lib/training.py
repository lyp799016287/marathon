# -*- coding: utf-8 -*-
from trainingbase import *
from urllib import urlencode
import pycurl,StringIO


class Training(TrainingBase):
	"""docstring for ClassName"""
	VDOT_API = "http://runsmartproject.com/vdot/app/api/find_paces"
	def __init__(self):
		super(TrainingBase, self).__init__()

	def get_from_runsmart(self,curl, url, data):
		result = ''
		header = [
		'Accept:application/json, text/javascript, */*; q=0.01',
		'Accept-Language:zh-CN,zh;q=0.8,en;q=0.6,zh-TW;q=0.4',
		'Cache-Control:max-age=0',
		'Connection:keep-alive',
		'Host:runsmartproject.com',
		'Referer:http://runsmartproject.com/calculator/embed/index.php?title=false',
		'Upgrade-Insecure-Requests:1',
		'User-Agent:Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.59 Safari/537.36',
		'X-Requested-With:XMLHttpRequest'
		]

		buf = StringIO.StringIO()
		# curl.setopt(pycurl.VERBOSE,1)
		# curl.setopt(pycurl.FOLLOWLOCATION, 1)
		# curl.setopt(pycurl.MAXREDIRS, 5)
		curl.setopt(pycurl.WRITEFUNCTION, buf.write)
		curl.setopt(pycurl.URL,url)
		curl.setopt(pycurl.POSTFIELDS,  urlencode(data))
		curl.setopt(pycurl.HTTPHEADER,header)
		curl.perform()
		result =buf.getvalue()
		buf.close()
		return result

	def get_VDOT(self,distance,times):
		params = {
			'distance':distance,
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
		url = self.VDOT_API+'?from=app'
		# for key in params:
		# 	url+="&%s=%s" % (key,params[key])

		# print url
		c = pycurl.Curl()
		body = self.get_from_runsmart(c,url,params)
		# print body

		return body



