# -*- coding: utf-8 -*-
import sys
sys.path.append("..")
from etc.setting import *
from lib.dber import *
import os,schedule
import json

class Reminder(DBer):
	def get_day_marathon(self,tDate):
		sql = "select * from t_marathon_calendar where `date` = '"+tDate+"'"
		rows = False
		try:
			# print sql
			# self.cursor.execute("set names utf8")
			self.cursor.execute(sql)
			rows = self.cursor.fetchall()
		except Exception as e:
			print(e)
		else:
			pass
		finally:
			pass
		return rows

	def get_recent_marathon(self):
		cDate = datetime.today()
		nDate = datetime.today() + timedelta(days=30)
		sql = "select *,TIMESTAMPDIFF(DAY,now(),`date`) as days from t_marathon_calendar where `date` between '"+cDate.strftime('%Y-%m-%d')+"' and '"+nDate.strftime('%Y-%m-%d')+"' limit 15"
		rows = False
		try:
			# print sql
			self.cursor.execute(sql)
			rows = self.cursor.fetchall()
		except Exception as e:
			print(e)
		else:
			pass
		finally:
			pass
		return rows

	def get_recent_all_marathon(self):
		cDate = datetime.today()
		nDate = datetime.today() + timedelta(days=60)
		sql = "select *,TIMESTAMPDIFF(DAY,now(),`date`) as days from t_marathon_calendar where `date` between '"+cDate.strftime('%Y-%m-%d')+"' and '"+nDate.strftime('%Y-%m-%d')+"' order by `date`"
		rows = False
		try:
			# print sql
			self.cursor.execute(sql)
			rows = self.cursor.fetchall()
		except Exception as e:
			print(e)
		else:
			pass
		finally:
			pass
		return rows

	def get_keep_sign_plan(self,plan_id):
		sqlpara = ''
		if plan_id:
			sqlpara = sqlpara + " and plan_id=%s" %plan_id
		sql = "select count(*) as keepdays from t_training_plan where 1=1" + sqlpara
		rows = False
		try:
			# print sql
			self.cursor.execute(sql)
			rows = self.cursor.fetchall()
		except Exception as e:
			print(e)
		else:
			pass
		finally:
			pass
		return rows


	# 获取提醒安排
	def get_schedule(self,remind_type):
		rows = False
		sql = "select * from t_user_reminder where remind_type=%d " % (remind_type)
		if remind_type==0:
			#每小时提醒，分
			ms = datetime.now().strftime('%M')
			sql +=" and time_format(remind_time,'%i')='"+ms+"'"
		elif remind_type ==1:
			# 单次提醒，时：分
			ms = datetime.now().strftime('%Y-%m-%d %H:%M')+':00'
			# 下面的日期格式空格别丢了
			sql +=" and CONCAT(date_format(now(),'%Y-%m-%d '),remind_time)='"+ms+"'"
		elif remind_type ==2:
			# 每天提醒 到时分
			ms = datetime.now().strftime('%H:%M')
			sql +=" and time_format(remind_time,'%H:%i')='"+ms+"'"
		elif remind_type ==3 or remind_type ==4 or remind_type ==5:
			# 每周，双周，每月提醒 到时分
			ms = datetime.now().strftime('%H:%M')
			sql +=" and time_format(remind_time,'%H:%i')='"+ms+"'"
		else:
			sql +=" and 1=2"

		try:
			# print sql
			self.cursor.execute(sql)
			rows = self.cursor.fetchall()
			# print rows
		except Exception as e:
			print(e)
			rows = False
		else:
			pass
		finally:
			pass

		return rows


	def countdown_days(self,tDate):
		cDate = ''
		left_days = 0
		if is_valid_date(tDate):
			cDate = datetime.strptime(tDate,"%Y-%m-%d")

		today = datetime.now()
		left_days = (cDate - today).days

		return left_days

	# 发送提醒
	def send_schedule_msg(self,openid,title,msg,name='亲，你有心的提醒',url = ''):
		tmpID = '05H3iz5lp6TtNrw_p98CEKTUvVNPv4dGu2Dv7XNdaGI'
		#access_token = client.fetch_access_token()
		client = WeChatClient(APPID, APP_SECRET)
		msg_data = {
			'first':{'value':name,'color':'#173177'},
			'keyword1':{'value':title,'color':'#173177'},
			'keyword2':{'value':datetime.now().strftime("%Y-%m-%d %H:%M:%S"),'color':'#173177'},
			'remark':{'value':msg,'color':'#173177'}
		}

		res = client.message.send_template(openid,tmpID,url,'#0000ff',msg_data)
		# print res


	# 发送提醒
	def send_schedule_msg_new(self,data):
		field_dict={}
		tmpID =  data['wxtmpid']
		openid = data['openid']
		url = data['url']
		# #access_token = client.fetch_access_token()
		client = WeChatClient(APPID, APP_SECRET)
		msg_data = {
			'first':{'value':data['first'],'color':'#173177'},
			'remark':{'value':data['remark'],'color':'#173177'},
		}
		field_arr = str(REMIND_MSG[data['id']]['field']).split(',')
		if len(field_arr)>0:
			for i in field_arr:
				field_dict[i]={'value':data[i],'color':'#173177'}
			msg_data = dict(msg_data,**field_dict)

		res = client.message.send_template(openid,tmpID,url,'#0000ff',msg_data)
		# print res



	def send_countdown_msg(self,tDate,user_id):
		left_days = self.countdown_days(tDate)
		msg = ''
		if left_days>0:
			msg = '距离 '+tDate+' 还剩：'+str(left_days)+'天'
		elif left_days == 0:
			msg = '今天就是 '+tDate+'啦'
		else:
			msg = tDate+' 已经过去：'+str(abs(left_days))+'天'
		is_marathon = self.get_day_marathon(tDate)
		remark = ''
		if is_marathon:
			i=0
			remark+= '这天有这么多马拉松比赛，祝你安全顺利PB！\n'
			for row in is_marathon:
				i+=1
				remark+= str(i)+'、'+row["name"].decode('utf-8',errors='ignore')+"\n日期："+row["date"].strftime('%Y-%m-%d')+'\n地点：'+row["addr"].decode('utf-8',errors='ignore')\
                 +'\n报名：'+row['site'].decode('utf-8',errors='ignore')+'\n'
		else:
			remark = '感谢使用倒计时服务，祝您跑步愉快！'

		tmpID = 'S-RZ05jpeGCtA5r5UHOnJlcASJ1AsrK7UnBMcVMphe8'

		client = WeChatClient(APPID, APP_SECRET)
		access_token = client.fetch_access_token()
		# print access_token
		msg_data = {
			'first':{'value':'我的倒计时','color':'#173177'},
			'keyword1':{'value':'倒计时','color':'#173177'},
			'keyword2':{'value':tDate,'color':'#173177'},
			'keyword3':{'value':msg,'color':'#173177'},
			'remark':{'value':remark,'color':'#173177'}
		}
		# print user_id
		res = client.message.send_template(user_id,tmpID,'http://x.lares.me/marathonlist','#0000ff',msg_data)
		# print res
