# -*- coding: utf-8 -*-
import os
import json
import schedule
from datetime import *
from lib.dber import *
from lib.lbs import *
from etc.setting import *
from reminder.reminder import *


global today, dber, tomorrow

#today = datetime.now().strftime('%Y-%m-%d')





reminder = Reminder()

def getRows (sql):
	dber = DBer()
	rs = False

	if str(sql) == '':
		pass
	else:
		print sql
		dber.cursor.execute('set names utf8')
		dber.cursor.execute(sql)
		rows = dber.cursor.fetchall()
		if rows:
			rs = rows

	return rs




#比赛日当天推送
def remindRaceDate():
	today = date.today()

	tomorrow = str(today + timedelta(days=1))
	sql = '''
		SELECT id, openid, name, project, race_date FROM t_plan_flow WHERE race_date ='%s' AND status = 1
	''' % today

	try:
		rows = getRows(sql)
		#print rows
		if rows:
			msg_info = REMIND_MSG['8']
			#print msg_info
			for row in rows:
				# 调用消息
				first = str(msg_info['first'])
				msg_data = {
					'id':'8',
					'wxtmpid':str(msg_info['tmpid']),
					'openid':row['openid'],
					'url':str(msg_info['url']),
					'first':first,
					'remark':'',
				}
				field_arr = str(msg_info['field']).split(',')
				field_value = ['马拉松目标日',str(today),'今天']
				if len(field_arr)>0:
					dict_field = dict(zip(field_arr,field_value))
					msg_data = dict(msg_data,**dict_field)
					reminder.send_schedule_msg_new(msg_data)

		else:
			print 'undo'
			pass

	except Exception as e:
		print (e)
	else:
		pass
	finally:
		pass

#明日训练项目推送
def remindTomorrowTraining():
	today = date.today()

	tomorrow = str(today + timedelta(days=1))
	lbs = Lbs()

	sql = '''
		SELECT a.id, a.openid, a.name, a.project, a.race_date, a.plan_info, b.openid, b.adcode FROM t_plan_flow a LEFT JOIN t_user b ON a.openid = b.openid WHERE a.start_date <='%s' AND a.end_date >='%s' AND a.status = 1 and a.push_status=1
	''' % (tomorrow,tomorrow)

	try:
		rows = getRows(sql)
		#print rows
		if rows:
			for r in rows:
				tr_name = ''
				rs_weather = ''

				if r['adcode']:		#天气预报
					rs_weather = lbs.get_tomorrow_weather(r['adcode'], tomorrow)
				plan_info = json.loads(r['plan_info'])
				for plan in plan_info:
					for it in plan['detail']:
						if str(it['date']) == tomorrow:
							tr_name = VDOT_NAME_CN[it['item']['name']]['name']
							#print tr_name

				if rs_weather !='':
					contents = rs_weather + '\n' + '\n' + '明日训练：' + tr_name + '\n'
				else:
					contents = '明日训练：' + tr_name + '\n'
				#print contents

				# 调用消息
				msg_info = REMIND_MSG['2']
				first = str(msg_info['first']).replace('{contents}', str(contents))
				msg_data = {
					'id':'2',
					'wxtmpid':str(msg_info['tmpid']),
					'openid':r['openid'],
					'url':str(msg_info['url']),
					'first':first,
					'remark':'',
				}
				field_arr = str(msg_info['field']).split(',')
				field_value = [r['name'],str(tomorrow)]

				if len(field_arr)>0:
					dict_field = dict(zip(field_arr,field_value))
					msg_data = dict(msg_data,**dict_field)
					reminder.send_schedule_msg_new(msg_data)

	except Exception as e:
		print (e)
	else:
		pass
	finally:
		pass

#倒计时提醒
def remindCountDown ():
	today = date.today()

	tomorrow = str(today + timedelta(days=1))
	sql = '''
		SELECT id, openid, name, race_date FROM t_plan_flow WHERE race_date >'%s' AND status = 1
	''' % today

	try:
		rows = getRows(sql)
		#print rows
		if rows:
			for row in rows:
				day_delta = (row['race_date'] - today).days
				#print str(row['race_date']) + '-----' + str((row['race_date'] - today).days)
				count_down = 0
				if day_delta in (1, 5, 10, 20, 30):
					count_down = day_delta
				else:
					pass

				if count_down != 0:
					#print count_down
					# 调用消息
					msg_info = REMIND_MSG['7']
					first = str(msg_info['first']).replace('{days}',str(count_down))
					msg_data = {
						'id':'7',
						'wxtmpid':str(msg_info['tmpid']),
						'openid':row['openid'],
						'url':str(msg_info['url']),
						'first':first,
						'remark':'',
					}
					field_arr = str(msg_info['field']).split(',')
					field_value = ['马拉松目标日',str(today),str(count_down)+'天']
					if len(field_arr)>0:
						dict_field = dict(zip(field_arr,field_value))
						msg_data = dict(msg_data,**dict_field)
						reminder.send_schedule_msg_new(msg_data)
	except Exception as e:
		print (e)
	else:
		pass
	finally:
		pass


def remindExpire5days():
	today = date.today()

	tomorrow = str(today + timedelta(days=1))
	sql = "SELECT a.id,a.date,c.openid,DATEDIFF(DATE_FORMAT(NOW(), '%Y-%m-%d'),a.DATE) AS expiredays,c.end_date FROM t_training_plan a JOIN (SELECT MAX(id) AS id FROM t_training_plan GROUP BY user_id) b ON a.id=b.id LEFT JOIN t_plan_flow c ON a.plan_id=c.id HAVING expiredays>0 AND MOD(expiredays,5)=0 AND end_date>=NOW()"
	rows = False
	try:
		rows = getRows(sql)
		if rows:
			for row in rows:
				openid = row['openid']
				expiredays = row['expiredays']

				#调用消息
		  		remind_msg = REMIND_MSG['4']
		  		first = str(remind_msg['first']).replace('{days}',str(expiredays))
				msg_data = {
					'id':'4',
					'wxtmpid':str(remind_msg['tmpid']),
					'openid':str(openid),
					'url':str(remind_msg['url']),
					'first':first,
					'remark':'',
				}
				field_arr = str(remind_msg['field']).split(',')
				field_value = ['马拉松训练',datetime.now().strftime('%Y-%m-%d')]
				if len(field_arr)>0:
					dict_field = dict(zip(field_arr,field_value))
					msg_data = dict(msg_data,**dict_field)
					reminder.send_schedule_msg_new(msg_data)

	except Exception as e:
		print (e)
	else:
		pass
	finally:
		pass

#当日未打卡提醒
def remindUnPunchIn ():
	today = date.today()

	tomorrow = str(today + timedelta(days=1))
	sql = '''
	SELECT a.id, a.openid, a.name, IFNULL(MAX(b.date), 0) AS tdate FROM t_plan_flow a LEFT JOIN t_training_plan b ON a.id = b.plan_id WHERE a.start_date <='%s' AND a.end_date >='%s' AND a.status = 1  and a.push_status=1 GROUP BY a.openid
	''' % (today,today)

	try:
		rows = getRows(sql)
		if rows:
			for row in rows:
				#print type(row['tdate'])
				if row['tdate'] != str(today):
					# 调用消息
					msg_info = REMIND_MSG['3']
					msg_data = {
						'id':'3',
						'wxtmpid':str(msg_info['tmpid']),
						'openid':row['openid'],
						'url':str(msg_info['url']),
						'first':str(msg_info['first']),
						'remark':'',
					}
					field_arr = str(msg_info['field']).split(',')
					field_value = ['马拉松训练',str(today)]
					if len(field_arr)>0:
						dict_field = dict(zip(field_arr,field_value))
						msg_data = dict(msg_data,**dict_field)
						reminder.send_schedule_msg_new(msg_data)


	except Exception as e:
		print (e)
	else:
		pass
	finally:
		pass



if __name__ == '__main__':
	# print ''
	schedule.every().day.at("06:00").do(remindRaceDate)
	schedule.every().day.at("09:00").do(remindCountDown)
	schedule.every().day.at("21:05").do(remindUnPunchIn)
	schedule.every().day.at("21:10").do(remindTomorrowTraining)
	schedule.every().day.at("21:00").do(remindExpire5days)
	while True:
		schedule.run_pending()
		time.sleep(1)
