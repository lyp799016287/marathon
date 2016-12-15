# -*- coding: utf-8 -*-
import sys
sys.path.append("..")
from etc.setting import *
from lib.dber import *
from lib.util import *

class User(DBer):
	# 注册用户基本信息（从授权接口获得）
	def reg_user(self,user_info):
		res = False
		openid = user_info['openid'].encode('utf-8',errors='ignore')
		name = user_info['nickname'].encode('utf-8',errors='ignore')
		gender = int(user_info['sex'])
		country = user_info['country'].encode('utf-8',errors='ignore')
		province = user_info['province'].encode('utf-8',errors='ignore')
		city = user_info['city'].encode('utf-8',errors='ignore')
		headimgurl = user_info['headimgurl'].encode('utf-8',errors='ignore')
		birthday = user_info['birthday'].encode('utf-8',errors='ignore')

		sql = """
		INSERT INTO `t_user` (
		`openid`,
		`name`,
		`gender`,
		`country`,
		`province`,
		`city`,
		`headimgurl`,
		`birthday`
		) values ('%s','%s', %d,'%s','%s','%s','%s','%s')""" % (openid,name,gender,country,province,city,headimgurl,birthday)

		rs = self.insert_sql(sql)

		if rs:
			res = rs

		return res

	# 添加用户留言
	def add_user_msg(self,user_info,msg):
		res = False
		sql = """
		INSERT INTO `marathon`.`t_user_msg`
		(
		`openid`,
		`nickname`,
		`msg`)
		VALUES
		(
		'%s','%s','%s'
		)
		""" % (user_info['openid'],user_info['nickname'],msg)

		rs = self.insert_sql(sql)

		if rs:
			res = rs

		return res

	# 添加测试流水
	def add_rankflow(self,ranker,total,percent,project,times,gender,user_info):
		res = False
		sql = """insert into t_ranker_flow (
				`openid`,
				`name`,
				`gender`,
				`project`,
				`ranker`,
				`total`,
				`percent`,
				`headimgurl`,
				`times`
				) values ('%s','%s','%s','%s','%s','%s','%s','%s','%s')""" % (user_info['openid'],user_info['name'],gender,project,ranker,total,percent,user_info['headimgurl'],times)
		rs = self.insert_sql(sql)

		if rs:
			res = rs

		return res

	# 添加测试流水
	def add_planflow(self,openid,plan_info):
		res = False

		#删除以前的计划
		del_sql = """ UPDATE t_plan_flow SET modify_time = now(), status = 0 WHERE openid='%s' """ % (openid)
		self.exe_sql(del_sql)

		sql = """
		INSERT INTO `marathon`.`t_plan_flow`
			(
			`openid`,
			`name`,
			`project`,
			`times`,
			`t_times`,
			`vdot_info`,
			`plan_info`,
			`race_date`,
			`start_date`,
			`end_date`
			)
			VALUES
			('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')
		""" % (openid,plan_info['user_info']['name'],plan_info['project'],':'.join(plan_info['hms']),':'.join(plan_info['ihms']),json.dumps(plan_info['vdot_info']),json.dumps(plan_info['plan']),plan_info['race_date'],plan_info['plan'][0]['detail'][0]['date'], plan_info['plan'][-1]['detail'][-1]['date'])
		rs = self.insert_sql(sql)

		if rs:
			res = rs

		return res

	def get_last_planflow(self,openid):
		res = False
		sql = "select * from t_plan_flow where openid='%s' and status = 1 order by id desc limit 1" % openid

		rs = self.get_row(sql)

		if rs:
			res = rs

		return res

	def get_planflow(self,planid):
		res = False
		sql = "select * from t_plan_flow where id=%d and status = 1 order by id desc limit 1" % planid

		rs = self.get_row(sql)

		if rs:
			res = rs

		return res

	# 添加VDOT测试流水
	def add_vdot_flow(self,openid,name,vdot_info):
		res = False
		sql = """
		INSERT INTO `marathon`.`t_vdot`
			(
			`openid`,
			`name`,
			`vdot_info`
			)
			VALUES
			('%s','%s','%s')
		""" % (openid,name,json.dumps(vdot_info))

		rs = self.insert_sql(sql)

		if rs:
			res = rs

		return res

	# 获取最新的VDOT测试数据
	def get_last_vdot(self,openid):
		res = False
		sql = "select * from t_vdot where openid='%s' and status = 1 order by id desc limit 1" % openid
		rs = self.get_row(sql)

		if rs:
			res = rs

		return res

	# 获取测试流水
	def get_rankflow(self,rankerid):
		res = False
		rankerid = int(rankerid)
		sql = "select * from t_ranker_flow where id=%d" % rankerid

		rs = self.get_row(sql)

		if rs:
			res = rs

		return res

	# 注册用户基本信息（从授权接口获得）
	# 已注册返回True，否则返回False
	def check_user_reg(self,user_info):
		res = False
		openid = user_info['openid']
		sql = "select count(*) as c from t_user where openid ='%s'" % (openid)

		row = self.get_row(sql)

		if int(row['c'])!=0:
			res = True

		return res

	# 修改用户基本信息（从授权接口获得）
	def update_user_info(self,user_info):
		res = False
		sql = """ update t_user set modify_time = now(),headimgurl='%s',name='%s',target_dist='%s',city='%s',province='%s',country='%s',gender=%d, birthday='%s' where openid='%s' """ % (user_info['headimgurl'],user_info['nickname'],json.dumps(user_info['target_dist'],ensure_ascii=False),user_info['city'],user_info['province'],user_info['country'],int(user_info['sex']),user_info['birthday'],user_info['openid'])

		rs = self.exe_sql(sql)

		if rs:
			res = True

		return res

	# 修改用户基本信息（修改生日）
	def update_user_birthday(self,user_info):
		res = False
		sql = """ update t_user set  birthday='%s' where openid='%s' """ % (user_info['birthday'],user_info['openid'])

		rs = self.exe_sql(sql)

		if rs:
			res = True

		return res
		
	# 修改用户基本信息（从授权接口获得）
	def update_user_adcode(self,openid,adcode):
		res = False

		sql = """ update t_user set adcode='%s',modify_time = now() where openid='%s' """ % (adcode,openid)

		rs = self.exe_sql(sql)

		if rs:
			res = True

		return res

	# 获取用户基本信息（从授权接口获得）
	def get_user_adcode(self,openid):
		res = False

		sql = """select adcode from  t_user  where openid='%s' """ % (openid)
		row = self.get_row(sql)
		if row != False:
			res = row['adcode']

		return res

	# 获取用户基本信息（从授权接口获得）
	def get_user_info(self,openid):
		res = False

		sql = """select * from  t_user  where openid='%s' """ % (openid)
		res = self.get_row(sql)
		return res

	# 获取自己的成绩排名情况
	def get_my_rank(self,project,gender,times,year=0):
		if 	project =='half_marathon':
			project ='半程'
		elif project =='marathon':
			project ='全程'
		else:
			project ='10公里'
		res = False
		sql = "select count(*) as ranker from t_marathon_score where project='%s' and gender='%s' and score_clean<='%s'" % (project,gender,times)
		if year!=0:
			sql+=" and year(`date`)='%s'" % year

		row = self.get_row(sql)

		if row != False:
			res = row['ranker']

		return res

	# 获取自己的成绩排名情况
	def get_all_count(self,project,gender,year=0):
		res = False
		sql = "select count(*) as total from t_marathon_score where project='%s' and gender='%s' " % (project,gender)
		if year!=0:
			sql+=" and year(`date`)='%s'" % year

		row = self.get_row(sql)

		if row != False:
			res = row['total']

		return res

	# 获取自己的成绩排名情况
	def get_race_forecast(self,project,times):
		res = False
		column_name = '`'+project+'`'

		sql = "select * from t_race_forecast where %s>='%s' order by %s limit 1" % (column_name,times,column_name)

		row = self.get_row(sql)

		if row != False:
			res = row

		return res

	# 获取自己的训练计划
	def get_my_plan(self,times,plan_type=1,start_date=''):
		res = False
		res_pace = False
		res_ret = []
		util = Util()
		# 先在target_pace中匹配各种训练的配速
		sql_pace = "select * from t_target_pace where marathon >= '%s' order by marathon limit 1" % times

		row = self.get_row(sql)

		if row != False:
			res_pace = row
		del(row)

		if res_pace:
			sql = """
				SELECT a.`id`,
				a.`plan_type`,
				case a.`plan_type`when 1 then '汉森初级18周计划' else '其他' end as plan_name,
				a.`week_seq`,
				a.`week_day`,
				a.`item_id`,
				b.item_name,
				a.`item_distance`,
				a.`item_duration`,
				a.`remark`
				FROM t_standard_plan as a left join t_training_item as b
				on a.item_id=b.id
				where a.`plan_type` = %d
				order by a.week_seq,a.week_day
				""" % plan_type

			rows = self.get_rows(sql)

			if rows != False:
				res = rows

				for row in res:
					row['pace']=''
					if row['item_id']==2:	#轻松跑
						row['pace']=res_pace['eazy_a']
					elif row['item_id']==3:	#速度跑，要看跑步距离
						if row['item_distance']<=5:
							row['pace']=res_pace['5k']
						elif row['item_distance']<=10:
							row['pace']=res_pace['10k']
						else:
							row['pace']=res_pace['10k']

					elif row['item_id']==4:	#节奏跑
						row['pace']=res_pace['rhythm']
					elif row['item_id']==5:	#力量跑
						row['pace']=res_pace['power']
					elif row['item_id']==6:	#长距离慢跑
						row['pace']=res_pace['lsd']
					if row['pace']!='':
						row['pace'] = str(row['pace'])[3:8]

			if res:
				cweek = 1
				week_info = []
				week_distance = 0.0
				days = len(res)
				i = 0
				if start_date!='':
					t_date = datetime.strptime(start_date,'%Y-%m-%d').date()
				for row in res:
					i+=1
					if start_date!='':
						c_date =(t_date-timedelta(days-i)).strftime('%m-%d')
						row['date'] = c_date
					# 添加星期几
					row['day_name'] = util.get_week_day(row['week_day'])

					if cweek != row['week_seq']:
						# 如果已经移动到第二周，将第一周信息作为整体追加到返回结果
						res_ret.append({'week':cweek,'detail':week_info,'week_distance':week_distance})
						# 重置循环信息
						cweek = row['week_seq']
						week_info = []
						week_distance = 0.0
					# 计算周跑量
					if row['item_distance']>0 and row['item_id']!=1:
						week_distance+=row['item_distance']
					# 将每天信息组合到周信息
					week_info.append(row)



				res_ret.append({'week':cweek,'detail':week_info,'week_distance':week_distance})

		return res_ret

	# 获取今天的训练安排
	def get_today_plan(self,openid,plan_id=0):
		today_plan = False
		today_date = datetime.now().strftime('%Y-%m-%d')
		# 查询计划、解析计划详情，对比日期，取出当天计划内容
		if plan_id!=0:
			plan_info = self.get_planflow(plan_id)
		else:
			plan_info = self.get_last_planflow(openid)
		if plan_info:
			all_plan = json.loads(plan_info['plan_info'])
			for w in all_plan:
				for d in w['detail']:
					if d['date'] == today_date:
						d['item']['cn_name'] = VDOT_NAME_CN[d['item']['name']]['name']
						today_plan = d
						today_plan['week'] = w['week']
						break
					else:
						continue

		return today_plan
	# 完成今日的训练安排
	def finish_today_plan(self,data):

		res = False
		sql = """
		INSERT INTO `t_training_plan` (
		`user_id`,
		`plan_id`,
		`weekseq`,
		`weekday`,
		`date`,
		`training_type`,
		`finish_time`
		) values (%d, %d, %s,%d,'%s',%d,'%s')""" % (data[0],data[1],data[2],data[3],data[4],data[5],data[6])

		rs = self.insert_sql(sql)

		if rs:
			res = rs

		return res
	def apdate_user_target(self,target,open):
		# print "TARGET",json.dumps(target, ensure_ascii=False, indent=2)
		sql="update  t_user set  target_dist='%s' where openid='%s'"%(json.dumps(target, ensure_ascii=False),open)
		res = False

		# sql = """ update t_user set adcode='%s',modify_time = now() where openid='%s' """ % (adcode,openid)
		try:
			# print sql
			self.cursor.execute(sql)
			self.db.commit()
			res = True
		except Exception as e:
			print(e)
			self.db.rollback()

		else:
			pass
		finally:
			pass

		return res
	def add_update_vdot(self,name,open,project,times,vdot_info):
		update_sql=""" update   t_vdot set status=0 where openid='%s'  """ %open
		rs = self.exe_sql(update_sql)

		if rs:
			res = True

		res = False
		# print "vdot",vdot_info
		sql = """
		INSERT INTO t_vdot
			(name,
			openid,
			project,
			time,
			vdot_info
			)
			VALUES
			('%s','%s','%s','%s',%s)
		""" % (name,open,project,times,json.dumps(vdot_info))
		try:
			# print sql
			self.cursor.execute(sql)
			res = self.cursor.lastrowid
			self.db.commit()

		except Exception as e:
			print(e)
			self.db.rollback()
		finally:
			pass
		return res

	def get_tmarathon_calendar(self,now_time):
		res=False
		sql="select * from t_marathon_calendar where date >='%s'" %now_time
		# print 'sql---------------',sql
		row = self.get_rows(sql)

		if row != False:
			res = row

		return res

	# 获取明天的训练安排
	def get_nextday_plan(self,openid,next_day,plan_id=0):
		today_plan = False
		today_date = next_day
		# 查询计划、解析计划详情，对比日期，取出当天计划内容
		if plan_id!=0:
			plan_info = self.get_planflow(plan_id)
		else:
			plan_info = self.get_last_planflow(openid)
		if plan_info:
			all_plan = json.loads(plan_info['plan_info'])
			for w in all_plan:
				for d in w['detail']:
					if d['date'] == today_date:
						d['item']['cn_name'] = VDOT_NAME_CN[d['item']['name']]['name']
						today_plan = d
						today_plan['week'] = w['week']
						break
					else:
						continue

		return today_plan

	def is_finish_plan(self,user_id,plan_id):
		res =False
		sql = "SELECT * FROM `t_training_plan` WHERE date = DATE_FORMAT(NOW(),'%%Y-%%m-%%d') and user_id ='%d' and plan_id ='%d' " %(user_id,plan_id)
		# print sql
		row = self.get_rows(sql)

		if row != False:
			res = row

		return res


	def get_baike_list(self,istop,newsid):
		res=False
		param = ""
		if istop==1:
			param=param+" and istop=1"
		if newsid!=0:
			param=param+" and id=%s" %newsid
		sql="select * from t_baike_list where 1=1" + param

		sql = sql + " order by id desc"
		# print 'sql---------------',sql
		row = self.get_rows(sql)

		if row != False:
			res = row

		return res

	# 变更用户计划推送状态
	def update_push_status(self,openid,pushstatus):
		res = False

		push_sql = """ UPDATE t_plan_flow SET modify_time = now(), push_status = %s WHERE openid='%s' """ % (pushstatus,openid)
		try:
			self.exe_sql(push_sql)
			res = True
		except Exception as e:
			print(e)
			self.db.rollback()
		else:
			pass
		finally:
			pass
		return res



