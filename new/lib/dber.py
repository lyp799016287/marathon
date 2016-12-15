# -*- coding: utf-8 -*-
from etc.setting import *
from util import *
import MySQLdb
import MySQLdb.cursors

class DBer():
	db = False
	cursor = False
	def __init__(self):
	    self.db = MySQLdb.connect(
	        host = DBCONFIG.get('host'),
	        user = DBCONFIG.get('user'),
	        passwd = DBCONFIG.get('passwd'),
	        db = DBCONFIG.get('db'),
	        charset = DBCONFIG.get('charset')
	    )
	    self.cursor = self.db.cursor(cursorclass = MySQLdb.cursors.DictCursor)

	#插入操作，返回lastInsertId/False
	def insert_sql(self, sql):
		res = False
		if sql.strip()=='':
			return res
		else:
			try:
				# print sql
				self.cursor.execute('set names utf8')
				self.cursor.execute(sql)
				res = self.cursor.lastrowid
				self.db.commit()

			except Exception as e:
				print(e)
				self.db.rollback()

			else:
				pass
			finally:
				pass

			return res

	#update/delete操作，返回True/False
	def exe_sql(self, sql):
		res = False
		if sql.strip()=='':
			return res
		else:
			try:
				# print sql
				self.cursor.execute('set names utf8')
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

	#fetchone()
	def get_row(self, sql):
		res = False
		if sql.strip()=='':
			return res
		else:
			try:
				# print sql
				self.cursor.execute('set names utf8')
				self.cursor.execute(sql)
				res = self.cursor.fetchone()

			except Exception as e:
				print(e)
			else:
				pass
			finally:
				pass

			return res

	#fetchall()
	def get_rows(self, sql):
		res = False
		if sql.strip()=='':
			return res
		else:
			try:
				# print sql
				self.cursor.execute('set names utf8')
				self.cursor.execute(sql)
				res = self.cursor.fetchall()

			except Exception as e:
				print(e)
			else:
				pass
			finally:
				pass

			return res

	def __del__(self):
	    self.db.close()







