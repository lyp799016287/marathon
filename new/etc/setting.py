# -*- coding: utf-8 -*-
# 通用设置及导入包和文件
import os,sys
reload(sys)
sys.setdefaultencoding('utf8')
from wechatpy import parse_message, create_reply
from wechatpy.client import WeChatClient
from wechatpy.utils import check_signature
from wechatpy.exceptions import (
    InvalidSignatureException,
    InvalidAppIdException,
)
from datetime import datetime,date,time,timedelta

HOST = "x.lares.me"

DBCONFIG = {'host':'192.168.16.221','user':'yzweb','passwd':'yzweb~123','db':'marathon','charset':'utf8'}
VDOT_API = "http://runsmartproject.com/vdot/app/api/find_paces"
TOKEN = os.getenv('WECHAT_TOKEN', 'YiZhiMarathon')
AES_KEY = os.getenv('WECHAT_AES_KEY', '0ZuBIvpV8EGmxXHLODxwvQROhPnp4KKWIiJCN6ibNW4')
APP_SECRET = os.getenv('WECHAT_APP_SECRET','9a8f4a02994fd50c163b4fd6913ef3f2')
APPID = os.getenv('WECHAT_APPID', 'wx4cbea4c0aa71212f')
NONCESTR = 'XW6PN429FQ0CG3LOK5RVM8AE7SZHY1UIJDBT'
WX_URL = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx4cbea4c0aa71212f"

WEEK = {'1':"周一",'2':'周二','3':'周三','4':'周四','5':'周五','6':'周六','7':'周日'}
VDOT_NAME = {'E':'Easy','I':'Interval','M':'Marathon','R':'Rep','T':'Threshold'}
PROJECT_NAME = {'10k':'10公里','half_marathon':'半程','marathon':'全程'}
VDOT_NAME_CN = {'0':{'name':'休息','sort':0},'E':{'name':'轻松跑','sort':1},'M':{'name':'马拉松配速跑','sort':2},'T':{'name':'乳酸门槛跑','sort':3},'I':{'name':'间歇训练','sort':4},'R':{'name':'重复训练','sort':5},'L':{'name':'LSD','sort':6},'X':{'name':'比赛日','sort':7}}
# VDOT_NAME_WEB = {'1':'轻松跑','2':'马拉松配速','3':'乳酸门槛','4':'间歇训练','5':'重复训练'}
sex_man="好身材、大长腿、要减肥、健康向上、要健美、要进步、帅气、男神、魅力、吸引、八块腹肌、高大、running man"
sex_woman="曼妙、好身材、要减肥、美腿、时尚、新运动、美好、女神、邻家、好性格、甜美、美人儿、running man"
run_hign="跑力小火车、风火轮、热血、无敌是多么寂寞~、还有谁？！、人中赤兔、赛刘翔、敏捷、速度与激情、一溜烟儿、一阵风、风驰电掣、身手敏捷、动如脱兔、奔逸绝尘、疾电奔星、迅雷不及掩耳、脚下生风、飞翔的感觉"
run_mid="潜力股、后劲足、爱奔跑、酷跑一族、带我飞、快快快、矫健、轻盈、飞奔、飞越、坚持不懈、努力、享受汗水、如沐春光、跑跑跑、跑起来、坚定的步伐、从容、收放自如"
run_low="要加油、坚定信念、坚毅、成功始于足下、不疾不徐、稳重、爱跑步、爱生活、慢跑人生、享受、求跑友、动起来、悠闲、忘我、慢即是快、哲学家"
age_hign="漫漫人生路、好身体、沉稳、快乐人生、参与、挑战自我、信念、习惯、日复一日、如一、激励、包容"
age_mid="保持身材、有耐力、恒心、大气、有信心、坚守、脚踏实地、责任、释放、回归本心、追寻、感悟、升华、归一"
age_low="朝气蓬勃、阳光、活力跑、速度、激情、热爱、年轻、跑出彩、动力、新一代、跑动生活、就爱跑、精力、灵动、青春"
DEFAULT_MSG = '''菜单说明：
<a href=\'/plan\'>我的计划</a>：定制和查看我的马拉松训练计划
<a href=\'/training\'>开始训练</a>：按计划训练
<a>马拉松</a>：提供马拉松相关的小工具（如天气预报）、近期马拉松列表等\n
从现在开始，您可以在此公众号上轻松订制马拉松训练计划啦，这个计划根据《丹尼尔斯经典跑步训练法》为您生成\n
如果已经生成了计划，点击“开始训练”菜单可以看到当天的训练安排\n
此外，在最后一个菜单“马拉松”中，还有些小工具，比如最近的马拉松有哪些，还可以查看当地的天气预报情况，这样可以更好的安排训练\n
这些功能都还很单薄，也还不太体系化，以后的每周里，会一点点持续丰富和优化\n
如果您想到一些好的点子、建议，不妨就在这里说出来，“我”会逐个阅读、细心考虑的，非常感谢您的关注╭❤～
'''
REMIND_MSG={
'1':{'title':'制定训练计划成功提醒','first':'撒花！你已经成功制定了马拉松训练计划，每天小马都会督促你好好完成训练计划哦，训练场上见~','tmpid':'LXUhM2Z8Se8Wap3YVLDI_ohqFRWeK5Bf0Ypd4fF_0Zg','field':'keyword1,keyword2','url':'http://x.lares.me/planlist'},
'2':{'title':'每日训练提醒','first':'{contents}','tmpid':'LXUhM2Z8Se8Wap3YVLDI_ohqFRWeK5Bf0Ypd4fF_0Zg','field':'keyword1,keyword2','url':'http://x.lares.me/training'},
'3':{'title':'当日未打卡提醒','first':'今天还没有进行训练打卡哦，生命在于尝试，更在于坚持。','tmpid':'G6jeN4jWSNZjwb1r-twf6UJLn4qVP4SlxAjgawXkxb8','field':'keyword1,keyword2','url':'http://x.lares.me/training'},
'4':{'title':'长时间未训练提醒','first':'快醒醒！你已经{days}天没有进行训练啦~良好的训练计划，能让你更轻松地完成马拉松目标，明天要准时起来训练哦~','tmpid':'G6jeN4jWSNZjwb1r-twf6UJLn4qVP4SlxAjgawXkxb8','field':'keyword1,keyword2','url':'http://x.lares.me/planlist'},
'5':{'title':'累积打卡提醒','first':'撒花！坚持训练{days}天成就达成！','tmpid':'-CDlIX9uKz6K1FlnYrlh9MLvyRZh6vmV9VF05PfxpJw','field':'keyword1,keyword2','url':'http://x.lares.me/showme'},
'6':{'title':'完成一项训练提醒','first':'撒花！马拉松训练计划达成！你已获得马拉松训练特殊荣誉勋章，棒棒哒~','tmpid':'-CDlIX9uKz6K1FlnYrlh9MLvyRZh6vmV9VF05PfxpJw','field':'keyword1,keyword2','url':'http://x.lares.me/showme'},
'7':{'title':'比赛倒计时提醒','first':'离马拉松目标日还有{days}天！跑步，去享受别人到不了的世界。','tmpid':'S-RZ05jpeGCtA5r5UHOnJlcASJ1AsrK7UnBMcVMphe8','field':'keyword1,keyword2,keyword3','url':'http://x.lares.me/planlist'},
'8':{'title':'比赛日提醒','first':'good luck！畅快跑！','tmpid':'S-RZ05jpeGCtA5r5UHOnJlcASJ1AsrK7UnBMcVMphe8','field':'keyword1,keyword2,keyword3','url':'http://x.lares.me/planlist'},
'9':{'title':'比赛报名提醒','first':'嗨！{project}将于{time}在上海举行，现在开始报名啦！点此信息去报名。','tmpid':'05H3iz5lp6TtNrw_p98CEKTUvVNPv4dGu2Dv7XNdaGI','field':'keyword1,keyword2','url':'http://x.lares.me/marathonlist'},
}
