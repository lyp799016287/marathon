# -*- coding: utf-8 -*-
import time, os ,sched,urllib,types

schedule = sched.scheduler(time.time, time.sleep) 

def perform_command(cmd, inc): 

    schedule.enter(inc, 0, perform_command, (cmd, inc)) 
    os.system(cmd) 

def timming_exe(cmd, inc = 60): 
    schedule.enter(inc, 0, perform_command, (cmd, inc)) 
    schedule.run() 

# def getHtml(url):
#     page = urllib.urlopen(url)
#     html = page.read()
#     return html

# html = getHtml("http://www.baidu.com")
# print html

# print("show time after 10 seconds:") 
# timming_exe("echo %time%", 10)
def normalize():
	name = input('please enter your name')
	print'hello,',name

# normalize()
def text():
	print'I\'m \"ok\"!'

def text1():
	a ='abc'
	b = a
	a = 'xyz'
	print type(a),b

def text2():
	print('enter your age')
	age = input()
	print type(age)
	if type(age) is types.IntType:
		if age >= 18:
			print('you are so old')
		else:
			print('you are so simple')
	else:
		print('error')

def text3():
	names = ['micheal','bob','tracy']
	for name in names:
		print name

def text4():
	a = 'abc'
	b = a.replace('a','A')
	print a,b

#格式化%s字符串，%d整数
def text5():
	print('Hi,%s,you are so handsome'%('liyapeng'))

def text6():
	pass
def text7(name,gender):
	print('name:',name)
	print('gender',gender)
def text8(numbers):
	sum = 0
	for n in numbers:
		sum = sum + n
	print sum
# 定义数组(list)	
def text9():
	L = [1,2,3]
	print L
#字典dict函数:把二维数组形成类似json格式的数据集合
def text10():
	names = ['Michael', 'Bob', 'Tracy']
	scores = [95, 75, 85]
	a = dict(zip(names,scores))
	print(a)
#交换两个变量的值
def text11():
	a = 1
	b = 2
	a,b = b,a
	print a,b
#数组直接叠加
def text12():
	a =[1,2,3]
	b =[4,5,6]
	c =a+b
	print c
#输出的是个二维数组:[(1,4),(2,5),(3,6)]
def text13():
	a = [1,2,3]
	b = [4,5,6]
	print zip(a,b)
def text14():
	L = []
	n = 1
	while n<=99:
		L.append(n)
		n = n+2
	print L
text14()