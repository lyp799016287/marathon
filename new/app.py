# -*- coding: utf-8 -*-
from __future__ import absolute_import, unicode_literals
import os
import random
import json

from flask import Flask, request, abort, render_template,redirect,session,escape, jsonify
from urllib import quote,unquote
from lib.weather import *
from lib.trainingbase import *
from etc.setting import *
from reminder.reminder import *
from lib.training import *
from models.user import *
from wechatpy.oauth import WeChatOAuth
from lib.lbs import *
from lib.util import *
import time
from werkzeug.routing import RequestRedirect



app = Flask(__name__)
SESSION_TYPE = 'filesystem'
SESSION_PERMANENT = True
SESSION_USE_SIGNER = True
app.config.update(SECRET_KEY='YiZhiStep')
app.permanent_session_lifetime = timedelta(minutes=30)

def get_source_userinfo(next_url,check_reg=True):
    o = WeChatOAuth(APPID, APP_SECRET,next_url)
    code = request.args.get('code', None)

    url = o.authorize_url
    #print 'o.authorize_url:%s' % url
    #print 'next_url:%s' % next_url

    access_token = ''
    openid = ''

    user_info = False
    #print '===========get_userinfo==========='
    #print code
    #print '===========get_userinfo==========='

    if code:
        try:
            token = o.fetch_access_token(code)
            #print token
            access_token = token['access_token']
            openid = token['openid']
        except Exception as e:
            print e

        if access_token!='' and openid!='':
            user_info = o.get_user_info(openid,access_token)
        if user_info and check_reg:
            user = User()
            if not user.check_user_reg(user_info):
                raise RequestRedirect('/reg')
            else:
                #user.update_user_info(user_info)
                session['username'] = user_info['nickname']
                session['openid'] = user_info['openid']
                session['headimgurl'] = user_info['headimgurl']


    return user_info
def  lable(vdots,age,gender):
    # print '年龄',age
    util=Util()
    gender_type,age_type,vdots_type='','',''
    res={}
    if vdots<30:
        vdots_type=run_low
    elif vdots>70:
        vdots_type=run_hign
    else:
        vdots_type=run_mid
    vdots_msg=util.get_show(vdots_type)
    if age<35:
        age_type=age_low
    elif age>50:
        age_type=age_hign
    else:
        age_type=age_mid
    age_msg=util.get_show(age_type)
    if gender==1:
        gender_type=sex_man
        sex_zc='男'
    else :
        gender_type=sex_woman
        sez_zc='女'
    sex_msg=util.get_show(gender_type)
    # print age_msg
    res={'age_msg':age_msg,'sex_msg':sex_msg,'vdots_msg':vdots_msg}
    return res
@app.route('/')
def index():
    user_info = {}

    if session.get('openid',False):
        user_info['nickname'] = session['username']
        user_info['headimgurl'] = session['headimgurl']

    #print user_info
    data = {'title':'马拉松训练日程','user_info':user_info}
    return render_template('index.htm', data=data)

@app.route('/racedate')
def racedate():
    date = request.args.get('date', None)
    code = -1
    msg = ''
    weeks = 0
    util = Util()
    if util.is_valid_date(date):
        _racedate = datetime.strptime(date,'%Y-%m-%d')
        _raceweek = int(time.strftime("%W",_racedate.timetuple()))
        _currweek = int(time.strftime("%W"))
        _raceyear = int(time.strftime("%Y",_racedate.timetuple()))
        _curryear = int(time.strftime("%Y",datetime.now().timetuple()))

        weeks = (_raceyear-_curryear)*52+_raceweek-_currweek+1
        if weeks <6:
            code = -2
            msg = '为确保安全训练，请务必选择6周以上的训练时长'
        else:
            code = 0
            msg = '距离比赛约还有%d周时间' % weeks
    else:
        code = -1
        msg = '非法日期'

    result = {'code':code,'date':date,'msg':msg,'weeks':weeks}

    return jsonify(result)

@app.route('/training')
def training():
    adcode=False
    weather = False
    url = request.url.replace(request.host,HOST)
    user_info = get_source_userinfo(url)
    nextday = request.args.get('nextday','0')
    nowdate = datetime.now().strftime('%Y-%m-%d')
    today_plan = False
    user = User()
    util = Util()

    uri = quote((url.decode('utf')).encode('utf-8'))
    if user_info==False and not session.get('openid',False):
        return redirect(WX_URL + '&redirect_uri=%s&response_type=code&scope=snsapi_userinfo&state=training#wechat_redirect' % uri)
    else:
        user_info = user.get_user_info(session['openid'])

    #print user_info
        plan = user.get_last_planflow(session['openid'])
        if plan == False:
            return redirect('vdot')
        date_time = request.args.get('date_time','0')
        # print nextday
        if nextday=='0' and date_time=='0':
            today_plan = user.get_today_plan(user_info['openid'])
        elif nextday=='1':
            morning = datetime.now()+timedelta(days = 1)
            next_day = morning.strftime('%Y-%m-%d')
            today_plan = user.get_nextday_plan(user_info['openid'],next_day)
        elif date_time!='0':
            today_plan = user.get_nextday_plan(user_info['openid'],date_time)
    adcode = user.get_user_adcode(user_info['openid'])
    lbs = Lbs()
    if adcode:
        weather = lbs.get_weather(adcode)
        #print weather
        weather = weather.split('\n')

    training_date = today_plan['date']
    weekseq = today_plan['week']
    weekday = util.get_weekdaynum_of_date(training_date)+1
    data = {'title':'开始训练','plan':today_plan,'user_info':user_info,'weather':weather,'nextday':nextday,'date_time':date_time,'nowdate':nowdate,'weekseq':weekseq,'weekday':weekday}
    plan_info = user.get_last_planflow(session['openid'])
    user_info = user.get_user_info(session['openid'])
    is_finish = user.is_finish_plan(user_info['id'],plan_info['id'])
    if len(is_finish) != 0:
        data['is_finish'] = 1
    else:
        data['is_finish'] = 0
    return render_template('training.htm', data=data)

@app.route('/finish_todaytrain',methods=['GET','POST'])
def finish_todaytrain():
    openid = session.get('openid')
    nextday = request.form.get('nextday',None)
    date_time = request.form.get('date_time','0')
    nowdate = datetime.now().strftime('%Y-%m-%d')
    # print date_time
    if nextday == '1':
        return jsonify({'code':-2,'msg':'还未到训练时间'})
    elif date_time !='0' and date_time != nowdate:
        return jsonify({'code':-3,'msg':'时间不合理'})

    user = User()
    util = Util()
    plan_info = user.get_last_planflow(openid)
    user_info = user.get_user_info(openid)
    is_finish = user.is_finish_plan(user_info['id'],plan_info['id'])
    # print len(is_finish)
    if len(is_finish) != 0:
        return jsonify({'code':-4,'msg':'你已经完成训练，无需重复操作!'})
    today_plan = user.get_today_plan(openid)
    nowdate = datetime.now().strftime('%Y-%m-%d')
    finish_time = datetime.now().strftime('%Y-%m-%d %X')
    training_type = VDOT_NAME_CN[today_plan['item']['name']]['sort']
    training_date = today_plan['date']
    weekseq = today_plan['week']
    weekday = util.get_weekdaynum_of_date(training_date)+1
    insdata = [user_info['id'],plan_info['id'],weekseq,weekday,training_date,training_type,finish_time]
    rs = user.finish_today_plan(insdata)

    if rs:
        # 发送提醒
        # 累计5天签到
        reminder = Reminder()
        getkeepdays = reminder.get_keep_sign_plan(plan_info['id'])[0]['keepdays']
        if getkeepdays%5 == 0:
            # 调用消息
            remind_msg = REMIND_MSG['5']
            first = str(remind_msg['first']).replace('{days}',str(getkeepdays))
            msg_data = {
                'id':'5',
                'wxtmpid':str(remind_msg['tmpid']),
                'openid':openid,
                'url':str(remind_msg['url']),
                'first':first,
                'remark':'',
            }
            field_arr = str(remind_msg['field']).split(',')
            field_value = ['坚持训练'+str(getkeepdays)+'天',datetime.now().strftime('%Y-%m-%d')]
            if len(field_arr)>0:
                dict_field = dict(zip(field_arr,field_value))
                msg_data = dict(msg_data,**dict_field)
                reminder.send_schedule_msg_new(msg_data)

        #完成一项训练提醒
        finish_date = json.loads(plan_info['plan_info'])[-1]['detail'][-1]['date']
        totalkeepdays=int((plan_info['end_date']-plan_info['start_date']).days)
        keepratio = round(float(getkeepdays) / float(totalkeepdays),2) * 100
        if str(finish_date)==str(datetime.today().strftime('%Y-%m-%d')) and keepratio>=80:
            #调用消息
            remind_msg = REMIND_MSG['6']
            first = str(remind_msg['first'])
            msg_data = {
                'id':'6',
                'wxtmpid':str(remind_msg['tmpid']),
                'openid':openid,
                'url':str(remind_msg['url']),
                'first':first,
                'remark':'',
            }
            field_arr = str(remind_msg['field']).split(',')
            field_value = ['完成训练计划',datetime.now().strftime('%Y-%m-%d')]
            if len(field_arr)>0:
                dict_field = dict(zip(field_arr,field_value))
                msg_data = dict(msg_data,**dict_field)
                reminder.send_schedule_msg_new(msg_data)

        return jsonify({'code': 1, 'msg':'成功完成训练','data':rs})
    else:
        return jsonify({'code': -1, 'msg':'失败'})

@app.route('/plan',methods=['GET', 'POST'])
def plan():
    url = request.url.replace(request.host,HOST)
    user_info = get_source_userinfo(url)
    vdot_info = False
    plan_info = False
    g_plan = False
    totalWeeks = False
    getVDOT = False
    hms = ['00','00','00']

    hours = []
    minutes = []
    seconds = []

    ihms = ['00','00','00']

    ihours = []
    iminutes = []
    iseconds = []
    race_date = ""
    project = ''

    # print user_info
    #print request.path

    uri = quote((url.decode('utf')).encode('utf-8'))
    if user_info==False and not session.get('openid',False):
        #print uri
        return redirect(WX_URL + '&redirect_uri=%s&response_type=code&scope=snsapi_userinfo&state=plan#wechat_redirect' % uri)

    user = User()
    util = Util()

    user_info = user.get_user_info(session['openid'])

    for i in range(0,60):
        i = util.prezero(str(i),2)
        minutes.append(i)
        seconds.append(i)
        iminutes.append(i)
        iseconds.append(i)
    for i in range(0,7):
        i = util.prezero(str(i),2)
        hours.append(i)
        ihours.append(i)

    planid = request.args.get('planid', None)
    if planid!=None:
        #print planid
        plan_info = user.get_planflow(int(str(planid)))
        if plan_info and plan_info['plan_info']:
            g_plan = json.loads(plan_info['plan_info'])
    else:
        plan_info = user.get_last_planflow(session['openid'])
        if plan_info and plan_info['plan_info']:
            planid = plan_info['id']
            g_plan = json.loads(plan_info['plan_info'])
        #print '------------------------------'
        #print user_info

    if plan_info:
        project = plan_info['project']
        times = plan_info['times']
        itimes = plan_info['t_times']
        vdot_info = json.loads(plan_info['vdot_info'])
        race_date = plan_info['race_date']
        hms = str(times).split(':')
        hms[0] = util.prezero(hms[0],2)
        #print hms

        ihms = str(itimes).split(':')
        ihms[0] = util.prezero(ihms[0],2)
        #print ihms
    else:
        race_date = datetime.strftime(datetime.today()+timedelta(days=42),'%Y-%m-%d')

    if vdot_info:
        #print '===============paces================='
        for  k,v in vdot_info['paces']['normal'].items():
            if len(k)>1:
                del vdot_info['paces']['normal'][k]
            else:
                #print k,v['k']
                vdot_info['paces']['normal'][VDOT_NAME_CN[k]['sort']] = vdot_info['paces']['normal'][k]
                vdot_info['paces']['normal'][VDOT_NAME_CN[k]['sort']]['name'] = VDOT_NAME_CN[k]['name']
                del vdot_info['paces']['normal'][k]


    if g_plan:
        totalWeeks = len(g_plan)
        for w in g_plan:
            # print '-----------------------weekinfo-----------------------'
            # print w
            week_dist = 0

            for d in w['detail']:
                d['item']['fName'] = VDOT_NAME_CN[d['item']['name']]['name']
                d['day_name'] = util.get_weekday_of_date(d['date'])
                d['simple_date'] = util.get_simple_date(d['date'])
                if d['item'].get('mins'):
                    pace_info = d['item'].get('pace')
                    if pace_info:
                        pace_info = pace_info.split(':')
                        t = timedelta(minutes=int(pace_info[0].split('.')[0]),seconds=int(pace_info[1].split('.')[0]))
                        week_dist+= int(d['item']['mins']) * 60.0 / t.seconds
                if d['item'].get('repeatDistance'):
                    week_dist+=int(d['item'].get('repeatDistance'))*d['item'].get('repeatTimes')*1.0/1000
            if w.get('week_dist'):
                week_dist += int(w['week_dist'])
                w['week_dist'] = round(week_dist)

        #print g_plan

    wx = util.get_wx_shareinfo(url)
    data = {'title':'马拉松测试成绩','user_info':user_info,'hours':hours,'minutes':minutes,'seconds':seconds,'hms':hms,'wx':wx,'project':project,'ihours':ihours,'iminutes':iminutes,'iseconds':iseconds,'ihms':ihms,'plan':g_plan,'race_date':race_date,'vdot_info':vdot_info,'planid':planid,'weeks':totalWeeks,'today':time.strftime('%Y-%m-%d')}

    return render_template('plan.htm', data=data)

@app.route('/planlist_new',methods=['GET', 'POST'])
def planlist_new():
    url = request.url.replace(request.host,HOST)
    user_info = get_source_userinfo(url)
    vdot_info = False
    plan_info = False
    g_plan = False
    totalWeeks = False
    getVDOT = False
    hms = ['00','00','00']
    db = DBer()
    hours = []
    minutes = []
    seconds = []

    ihms = ['00','00','00']

    ihours = []
    iminutes = []
    iseconds = []
    race_date = ""
    project = ''

    # print user_info
    #print request.path

    uri = quote((url.decode('utf')).encode('utf-8'))
    if user_info==False and not session.get('openid',False):
        #print uri
        return redirect(WX_URL + '&redirect_uri=%s&response_type=code&scope=snsapi_userinfo&state=planlist#wechat_redirect' % uri)

    user = User()
    util = Util()

    user_info = user.get_user_info(session['openid'])
    plan = user.get_last_planflow(session['openid'])
    if plan == False:
        return redirect('vdot')
    # print plan
    for i in range(0,60):
        i = util.prezero(str(i),2)
        minutes.append(i)
        seconds.append(i)
        iminutes.append(i)
        iseconds.append(i)
    for i in range(0,7):
        i = util.prezero(str(i),2)
        hours.append(i)
        ihours.append(i)

    planid = request.args.get('planid', None)
    if planid!=None:
        #print planid
        plan_info = user.get_planflow(int(str(planid)))
        if plan_info and plan_info['plan_info']:
            g_plan = json.loads(plan_info['plan_info'])
    else:
        plan_info = user.get_last_planflow(session['openid'])
        if plan_info and plan_info['plan_info']:
            planid = plan_info['id']
            g_plan = json.loads(plan_info['plan_info'])
        #print '------------------------------'
        #print user_info

    if plan_info:
        project = plan_info['project']
        times = plan_info['times']
        itimes = plan_info['t_times']
        vdot_info = json.loads(plan_info['vdot_info'])
        race_date = plan_info['race_date']
        hms = str(times).split(':')
        hms[0] = util.prezero(hms[0],2)
        #print hms

        ihms = str(itimes).split(':')
        ihms[0] = util.prezero(ihms[0],2)
        #print ihms
    else:
        race_date = datetime.strftime(datetime.today()+timedelta(days=42),'%Y-%m-%d')

    if vdot_info:
        #print '===============paces================='
        for  k,v in vdot_info['paces']['normal'].items():
            if len(k)>1:
                del vdot_info['paces']['normal'][k]
            else:
                #print k,v['k']
                vdot_info['paces']['normal'][VDOT_NAME_CN[k]['sort']] = vdot_info['paces']['normal'][k]
                vdot_info['paces']['normal'][VDOT_NAME_CN[k]['sort']]['name'] = VDOT_NAME_CN[k]['name']
                del vdot_info['paces']['normal'][k]


    if g_plan:
        totalWeeks = len(g_plan)
        for w in g_plan:
            # print '-----------------------weekinfo-----------------------'
            # print w
            week_dist = 0

            for d in w['detail']:
                d['item']['fName'] = VDOT_NAME_CN[d['item']['name']]['name']
                d['day_name'] = util.get_weekday_of_date(d['date'])
                d['simple_date'] = util.get_simple_date(d['date'])
                if d['item'].get('mins'):
                    pace_info = d['item'].get('pace')
                    if pace_info:
                        pace_info = pace_info.split(':')
                        t = timedelta(minutes=int(pace_info[0].split('.')[0]),seconds=int(pace_info[1].split('.')[0]))
                        week_dist+= int(d['item']['mins']) * 60.0 / t.seconds
                if d['item'].get('repeatDistance'):
                    week_dist+=int(d['item'].get('repeatDistance'))*d['item'].get('repeatTimes')*1.0/1000
            if w.get('week_dist'):
                week_dist += int(w['week_dist'])
                w['week_dist'] = round(week_dist)

        #print g_plan
    sql = """SELECT COUNT(finish_status=2 OR NULL) AS keepdays FROM t_training_plan
             WHERE plan_id=%s""" % plan_info['id']
    rs = db.get_row(sql)

    plan_info['keepdays'] = str(rs['keepdays'])
    firstday = g_plan[0]['detail'][0]['date']
    plan_info['firstday'] = firstday
    s_date = datetime.strptime(str(firstday),'%Y-%m-%d')
    r_date = datetime.strptime(str(plan_info['race_date']),'%Y-%m-%d')
    t_date = datetime.today()
    plan_info['remaindays'] = str((r_date - t_date).days) + ' 天'
    plan_info['progress'] = str(int(round(int(plan_info['keepdays']) * 1.0  / (r_date - s_date).days * 1.0,2) * 100)) +"%"
    wx = util.get_wx_shareinfo(url)
    data = {'title':'马拉松测试成绩','user_info':user_info,'hours':hours,'minutes':minutes,'seconds':seconds,'hms':hms,'wx':wx,'project':project,'ihours':ihours,'iminutes':iminutes,'iseconds':iseconds,'ihms':ihms,'plan':g_plan,'race_date':race_date,'vdot_info':vdot_info,'planid':planid,'weeks':totalWeeks,'today':time.strftime('%Y-%m-%d'),'plan_info':plan_info}
    return render_template('planlist_new.htm', data=data)

@app.route('/planlist',methods=['GET', 'POST'])
def planlist():
    url = request.url.replace(request.host,HOST)
    user_info = get_source_userinfo(url)
    vdot_info = False
    plan_info = False
    g_plan = False
    totalWeeks = False
    getVDOT = False
    hms = ['00','00','00']

    hours = []
    minutes = []
    seconds = []

    ihms = ['00','00','00']

    ihours = []
    iminutes = []
    iseconds = []
    race_date = ""
    project = ''

    # print user_info
    #print request.path

    uri = quote((url.decode('utf')).encode('utf-8'))
    if user_info==False and not session.get('openid',False):
        #print uri
        return redirect(WX_URL + '&redirect_uri=%s&response_type=code&scope=snsapi_userinfo&state=planlist#wechat_redirect' % uri)

    user = User()
    util = Util()

    user_info = user.get_user_info(session['openid'])
    plan = user.get_last_planflow(session['openid'])
    if plan == False:
        return redirect('vdot')
    # print plan
    for i in range(0,60):
        i = util.prezero(str(i),2)
        minutes.append(i)
        seconds.append(i)
        iminutes.append(i)
        iseconds.append(i)
    for i in range(0,7):
        i = util.prezero(str(i),2)
        hours.append(i)
        ihours.append(i)

    planid = request.args.get('planid', None)
    if planid!=None:
        #print planid
        plan_info = user.get_planflow(int(str(planid)))
        if plan_info and plan_info['plan_info']:
            g_plan = json.loads(plan_info['plan_info'])
    else:
        plan_info = user.get_last_planflow(session['openid'])
        if plan_info and plan_info['plan_info']:
            planid = plan_info['id']
            g_plan = json.loads(plan_info['plan_info'])
        #print '------------------------------'
        #print user_info

    if plan_info:
        project = plan_info['project']
        times = plan_info['times']
        itimes = plan_info['t_times']
        vdot_info = json.loads(plan_info['vdot_info'])
        race_date = plan_info['race_date']
        hms = str(times).split(':')
        hms[0] = util.prezero(hms[0],2)
        #print hms

        ihms = str(itimes).split(':')
        ihms[0] = util.prezero(ihms[0],2)
        #print ihms
    else:
        race_date = datetime.strftime(datetime.today()+timedelta(days=42),'%Y-%m-%d')

    if vdot_info:
        #print '===============paces================='
        for  k,v in vdot_info['paces']['normal'].items():
            if len(k)>1:
                del vdot_info['paces']['normal'][k]
            else:
                #print k,v['k']
                vdot_info['paces']['normal'][VDOT_NAME_CN[k]['sort']] = vdot_info['paces']['normal'][k]
                vdot_info['paces']['normal'][VDOT_NAME_CN[k]['sort']]['name'] = VDOT_NAME_CN[k]['name']
                del vdot_info['paces']['normal'][k]


    if g_plan:
        totalWeeks = len(g_plan)
        for w in g_plan:
            # print '-----------------------weekinfo-----------------------'
            # print w
            week_dist = 0

            for d in w['detail']:
                d['item']['fName'] = VDOT_NAME_CN[d['item']['name']]['name']
                d['day_name'] = util.get_weekday_of_date(d['date'])
                d['simple_date'] = util.get_simple_date(d['date'])
                if d['item'].get('mins'):
                    pace_info = d['item'].get('pace')
                    if pace_info:
                        pace_info = pace_info.split(':')
                        t = timedelta(minutes=int(pace_info[0].split('.')[0]),seconds=int(pace_info[1].split('.')[0]))
                        week_dist+= int(d['item']['mins']) * 60.0 / t.seconds
                if d['item'].get('repeatDistance'):
                    week_dist+=int(d['item'].get('repeatDistance'))*d['item'].get('repeatTimes')*1.0/1000
            if w.get('week_dist'):
                week_dist += int(w['week_dist'])
                w['week_dist'] = round(week_dist)

        #print g_plan

    wx = util.get_wx_shareinfo(url)
    data = {'title':'马拉松测试成绩','user_info':user_info,'hours':hours,'minutes':minutes,'seconds':seconds,'hms':hms,'wx':wx,'project':project,'ihours':ihours,'iminutes':iminutes,'iseconds':iseconds,'ihms':ihms,'plan':g_plan,'race_date':race_date,'vdot_info':vdot_info,'planid':planid,'weeks':totalWeeks,'today':time.strftime('%Y-%m-%d')}
    return render_template('planlist.htm', data=data)
@app.route('/ranker',methods=['GET', 'POST'])
def ranker():

    url = request.url.replace(request.host,HOST)
    user_info = get_source_userinfo(url)

    #print user_info
    #print request.path

    uri = quote((url.decode('utf')).encode('utf-8'))
    if user_info==False and not session.get('openid',False):
        return redirect(WX_URL + '&redirect_uri=%s&response_type=code&scope=snsapi_userinfo&state=ranker#wechat_redirect' % uri)


    user = User()

    util = Util()
    wx = util.get_wx_shareinfo(url)

    user_info = user.get_user_info(session['openid'])
    hours = []
    minutes = []
    seconds = []
    year = 2015
    for i in range(0,60):
        i = util.prezero(str(i),2)
        minutes.append(i)
        seconds.append(i)
    for i in range(0,7):
        i = util.prezero(str(i),2)
        hours.append(i)

    hms = ['00','00','00']
    if request.method == 'POST':
        gender = request.form.get('gender', None).encode('utf-8',errors='ignore')
        project = request.form.get('project', None).encode('utf-8',errors='ignore')
        # times = request.form.get('times', None)
        # if times == None:
        #     print 'times is none'
        times =  request.form.get('hour', None)+':'+ request.form.get('minute', None)+ ':'+ request.form.get('second', None)

        #print gender,project,times


        ranker = user.get_my_rank(project,gender,times,2015)
        #print ranker
        total = user.get_all_count(project,gender,2015)
        #print total
        percent = ('%.2f' %((1 - ranker*1.00 / total) * 100))


        hms = times.split(':')
        #print hms

        data = {'title':'马拉松测试成绩','ranker':{'ranker':ranker,'total':total,'percent':percent},'project':project,'gender':gender,'times':times.strip(' '),'year':year,'user_info':user_info,'hours':hours,'minutes':minutes,'seconds':seconds,'hms':hms,'wx':wx}
        # 记录日志
        rankerid = user.add_rankflow(ranker,total,percent,project,times.strip(' '),gender,user_info)
        #print rankerid
        return redirect('/ranker?rankerid=%d' % rankerid)
    else:
        rankerid = request.args.get('rankerid', 0)
        if rankerid!=0:
            rankerinfo = user.get_rankflow(rankerid)
            if rankerinfo:
                ranker = rankerinfo['ranker']
                total = rankerinfo['total']
                percent = rankerinfo['percent']
                project = rankerinfo['project']
                gender = rankerinfo['gender']
                times = rankerinfo['times']
                ranker = rankerinfo['ranker']
                user_info['name'] = rankerinfo['name']
                user_info['headimgurl'] = rankerinfo['headimgurl']
                hms = str(times).split(':')
                hms[0] = util.prezero(hms[0],2)
                hms[1] = util.prezero(hms[1],2)
                hms[2] = util.prezero(hms[2],2)
                #print hms
                # 获取某一个ranker对应的测试结果页
                data = {'title':'马拉松测试成绩','ranker':{'ranker':ranker,'total':total,'percent':percent},'project':project,'gender':gender,'times':times,'year':year,'user_info':user_info,'hours':hours,'minutes':minutes,'seconds':seconds,'hms':hms,'rankerid':rankerid,'wx':wx}
            else:
                gender = '男'
                if user_info['gender'] == 2:
                    gender = '女'
                data = {'title':'马拉松测试成绩','ranker':False,'user_info':user_info,'hours':hours,'minutes':minutes,'seconds':seconds,'hms':hms,'wx':wx,'gender':gender}
        else:
            gender = '男'
            if user_info['gender'] == 2:
                gender = '女'

            data = {'title':'马拉松测试成绩','ranker':False,'user_info':user_info,'hours':hours,'minutes':minutes,'seconds':seconds,'hms':hms,'wx':wx,'gender':gender}

    return render_template('ranker.htm', data=data)

@app.route('/marathonlist')
def marathonlist():
    _url = request.url.replace(request.host,HOST)
    user_info = get_source_userinfo(_url)
    reminder = Reminder()
    result = reminder.get_recent_all_marathon()
    data = {'title':'最近60天马拉松列表','dlist':result}

    return render_template('marathonlist.htm', data=data)

@app.route('/cuplist')
def cuplist():
    url = request.url.replace(request.host,HOST)
    uri = quote((url.decode('utf')).encode('utf-8'))
    user_info = get_source_userinfo(url)
    db = DBer()
    user = User()
    data = {}

    if user_info==False and not session.get('openid',False):
        #print uri
        return redirect(WX_URL + '&redirect_uri=%s&response_type=code&scope=snsapi_userinfo&state=showme#wechat_redirect' % uri)
    else:
        user_info = user.get_user_info(session.get('openid'))

    plan_info = user.get_last_planflow(session.get('openid'))

    if plan_info and plan_info['plan_info']:
        g_plan = json.loads(plan_info['plan_info'])
        totalweek = len(g_plan)
        firstday = g_plan[0]['detail'][0]['date']
        plan_info['totalweek'] = totalweek
        plan_info['firstday'] = firstday
        sql = """SELECT COUNT(finish_status=2 OR NULL) AS keepdays FROM t_training_plan
                 WHERE plan_id=%s""" % plan_info['id']
        rs = db.get_row(sql)

        plan_info['keepdays'] = str(rs['keepdays'])
        #判断是否完成一项训练里程碑
        finish_date = json.loads(plan_info['plan_info'])[-1]['detail'][-1]['date']
        totalkeepdays=int((plan_info['end_date']-plan_info['start_date']).days)
        keepratio = round(float(plan_info['keepdays']) / float(totalkeepdays),2) * 100
        if str(finish_date)==str(datetime.today().strftime('%Y-%m-%d')) and keepratio>=80:
            plan_info['trainingdays'] = 'bingo'
        else:
            sql1 = """SELECT COUNT(finish_status=2 OR NULL) AS trainingdays FROM t_training_plan
                     WHERE user_id=%s""" % user_info['id']
            rs1 = db.get_row(sql1)
            plan_info['trainingdays'] = int(rs1['trainingdays'])

    data = {'title':'获得荣誉','plan_info':plan_info}

    return render_template('cup_list.htm', data=data)

@app.route('/wechat', methods=['GET', 'POST'])
def wechat():
    signature = request.args.get('signature', '')
    timestamp = request.args.get('timestamp', '')
    nonce = request.args.get('nonce', '')
    encrypt_type = request.args.get('encrypt_type', 'raw')
    msg_signature = request.args.get('msg_signature', '')
    try:
        check_signature(TOKEN, signature, timestamp, nonce)
    except InvalidSignatureException:
        abort(403)
    if request.method == 'GET':
        echo_str = request.args.get('echostr', '')
        return echo_str



    # POST request
    if encrypt_type == 'raw':
        # plaintext mode
        reminder = Reminder()
        msg = parse_message(request.data)
        client = WeChatClient(APPID, APP_SECRET)
        c = DEFAULT_MSG
        user_info = client.user.get(msg.source)
        #print user_info
        if msg.type == 'text':
            #print msg.content

            user = User()
            user.add_user_msg(user_info,msg.content)

            c = '来信已收到，\n我会尽快查看、回复。\n谢谢！❤'
            reply = create_reply(c, msg)

        elif msg.type == 'image':
            c = '来信已收到，\n我会尽快查看、回复。\n谢谢！❤'
            reply = create_reply(c, msg)
        elif msg.type == 'event':
            if msg.event == 'subscribe':
                c = DEFAULT_MSG
                reply = create_reply(c, msg)

                reminder.send_schedule_msg(openid='oPd_yvkE32j7rfdAUOSmEiuL-OtU',title='新增关注：%s'%user_info['nickname'],msg="新增一个关注",name='新增关注：%s'%user_info['nickname'],url='https://mp.weixin.qq.com/cgi-bin/home?t=wap_templates/home/index&lang=zh_CN&mod=wap')
            elif msg.event == 'unsubscribe':
                c = '故人西辞黄鹤楼'
                reply = create_reply(c, msg)
                reminder.send_schedule_msg(openid='oPd_yvkE32j7rfdAUOSmEiuL-OtU',title='丢失关注',msg="丢失一个关注",name='丢失关注',url='https://mp.weixin.qq.com/cgi-bin/home?t=wap_templates/home/index&lang=zh_CN&mod=wap')
                #print c
            elif msg.event == 'location':
                #获取地理位置
                latitude = msg._data['Latitude']
                longitude = msg._data['Longitude']
                #print 'Latitude:'+latitude+'Longitude:'+longitude
                lbs = Lbs()
                # 获得位置码
                ad = lbs.get_geo(longitude,latitude)
                user = User()
                user.update_user_adcode(msg.source,ad)
                # c = lbs.get_weather(ad)
                reply = create_reply('', msg)


            elif msg.event =='click':
                #print msg
                # print msg._data['EventKey']
                event_key = msg._data['EventKey']
                # 菜单点击
                if event_key =='V1001_START_TRAINING':
                    c = getWeatherResult('上海')
                elif event_key =='V1001_TOMORROW_WEATHER':
                    c = getWeatherResult('上海')
                elif event_key =='V1001_GOOD':
                    c = '☀你的鼓励是我进步的动力！'
                elif event_key =='V1001_WEATHER':
                    user = User()
                    adcode = user.get_user_adcode(msg.source)
                    lbs = Lbs()
                    c = lbs.get_weather(adcode)
                    c += '\n- - - - - - - - - - - - - - - -\n'+lbs.get_weather_later(adcode)
                else:
                    c = '♣还在调试中，敬请期待！'
                reply = create_reply(c, msg)
            else:
                reply = create_reply('Sorry, can not handle this for now', msg)
        else:
            reply = create_reply('Sorry, can not handle this for now', msg)

        if msg.type in ['text','image']:
            reminder.send_schedule_msg(openid='oPd_yvkE32j7rfdAUOSmEiuL-OtU',title='%s来信了'%user_info['nickname'],msg="信件内容：\n%s"%msg.content,name='%s来信了'%user_info['nickname'],url='https://mp.weixin.qq.com/cgi-bin/home?t=wap_templates/home/index&lang=zh_CN&mod=wap')

        return reply.render()
    else:
        # encryption mode
        from wechatpy.crypto import WeChatCrypto

        crypto = WeChatCrypto(TOKEN, AES_KEY, APPID)
        try:
            msg = crypto.decrypt_message(
                request.data,
                msg_signature,
                timestamp,
                nonce
            )
        except (InvalidSignatureException, InvalidAppIdException):
            abort(403)
        else:
            msg = parse_message(msg)
            if msg.type == 'text':
                if "天气" in msg.content:
                    c = getWeatherResult(msg.content.replace('天气',''))
                else:
                    c = msg.content

                reply = create_reply(c, msg)
                #reply = create_reply(msg.content, msg)
            else:
                reply = create_reply('Sorry, can not handle this for now', msg)
            return crypto.encrypt_message(reply.render(), nonce, timestamp)

@app.route('/makeplan',methods=['GET','POST'])
def make_plan():
    #print session['openid'] + '----' + session['username']
    #print request.form.get('project', '')
    #print request.values['project']
    #result = {'code':1,'msg':'success'}
    url = request.url.replace(request.host,HOST)
    user_info = get_source_userinfo(url)

    uri = quote((url.decode('utf')).encode('utf-8'))
    if user_info==False and not session.get('openid',False):
        return redirect(WX_URL + '&redirect_uri=%s&response_type=code&scope=snsapi_userinfo&state=ranker#wechat_redirect' % uri)

    user = User()
    util = Util()

    user_info = user.get_user_info(session['openid'])
    project = request.form.get('project', None)
    times =  request.form.get('hour', None)+':'+ request.form.get('minute', None)+ ':'+ request.form.get('second', None)
    itimes =  request.form.get('ihour', None)+':'+ request.form.get('iminute', None)+ ':'+ request.form.get('isecond', None)

    race_date =  request.form.get('race_date', '')
    if not util.is_valid_date(race_date):
        return jsonify({'code': -1, 'msg': '请选择正确的比赛日期'})

    hms = times.split(':')
    #print hms

    ihms = itimes.split(':')

    # 计算最终日期距离现在时间长度
    r_date = datetime.strptime(race_date,'%Y-%m-%d')
    t_date = datetime.today()

    days = (r_date - t_date).days

    totalWeeks = days // 7

    if days % 7 > 0:
        totalWeeks += 1

    generator = TrainingPlanGenerator(totalWeeks=totalWeeks,raceDate=race_date)
    generator.generatePlan()
    g_plan = generator.getTrainingPlan()
    # print g_plan

    #print g_plan[0]['detail'][0]['date']
    #print g_plan[-1]['detail'][-1]['date']
    t = Training()
    tp = util.get_kms(project)
    vdot_info= t.get_VDOT(tp,times)
    save_vdot = json.loads(vdot_info)

    if save_vdot:
        # vdotid = user.add_vdot_flow(user_info['openid'],user_info['name'],save_vdot)
        if g_plan:
            for w in g_plan:
                # print '-----------------------weekinfo-----------------------'
                # print w
                for d in w['detail']:
                    pace = ''
                    if d['item']['name'] in ('E','M','T','I','R'):
                        pace = save_vdot['paces']['normal'][d['item']['name']]['k']
                    if d['item']['name']=='L':
                        pace = save_vdot['paces']['normal']['M']['k']
                    if pace!='':
                        d['item']['pace'] = pace
        # 保存计划流水
        data = {'user_info':user_info,'hms':hms,'project':project,'ihms':ihms,'vdot_info':save_vdot,'plan':g_plan,'race_date':race_date}

        planid = user.add_planflow(user_info['openid'],data)
        # 制定训练计划成功提醒
        if planid and project!='':
            reminder = Reminder()
            openid = session.get('openid')
            remind_msg = REMIND_MSG['1']
            first = str(remind_msg['first'])
            msg_data = {
                'id':'1',
                'wxtmpid':str(remind_msg['tmpid']),
                'openid':openid,
                'url':str(remind_msg['url']),
                'first':first,
                'remark':'',
            }
            field_arr = str(remind_msg['field']).split(',')
            field_value = [session['username'],datetime.now().strftime('%Y-%m-%d')]
            if len(field_arr)>0:
                dict_field = dict(zip(field_arr,field_value))
                msg_data = dict(msg_data,**dict_field)
                reminder.send_schedule_msg_new(msg_data)

        return jsonify({'code': 1, 'msg': 'success', 'insert_id':planid})
    else:
        return jsonify({'code': -2, 'msg': '服务器繁忙，暂未生成计划，请稍后再试'})

#静态文件
@app.route('/text')
def text():
    return app.send_static_file('html/text.htm')


@app.route('/reg',methods=['GET', 'POST'])
def reg ():
    url = request.url.replace(request.host,HOST)

    user_info = False

    user = User()

    if request.method == 'POST':
        util = Util()


        user_data = {}

        user_data['sex'] = request.form.get('gender', None)
        user_data['birthday'] = request.form.get('birthday', None)
        user_data['openid'] = session['openid']
        user_data['nickname'] = session['username']
        user_data['headimgurl'] = session['headimgurl']
        user_data['country'] = session['country']
        user_data['province'] = session['province']
        user_data['city'] = session['city']

        res = False

        if not util.is_valid_date(user_data['birthday']):
            return jsonify({'code': -1, 'msg': '出生日期格式不正确'})

        if not user_data['openid']:
            redirect('/reg')

        if not user.check_user_reg(user_data):
            res = user.reg_user(user_data)
        elif not user.get_last_vdot(user_data['openid']):
            res=user.update_user_birthday(user_data)
        else:
            birthday=str(user_data['birthday']).split('-')
            nowday=str(time.strftime("%Y-%m-%d", time.localtime())).split('-')
            age=int(nowday[0])-int(birthday[0])+1
            rs_vdot = user.get_last_vdot(user_data['openid'])
            vdots = json.loads(rs_vdot['vdot_info'])['vdot']
            user_data['target_dist'] = lable(vdots,age,user_data['sex'])
            res = user.update_user_info(user_data)

        # if res:
        #     del(session['country'])
        #     del(session['province'])
        #     del(session['city'])

        return jsonify({'code': 1, 'msg': 'success', 'openid':user_data['openid'], 'username':user_data['nickname'], 'headimgurl':user_data['headimgurl']})
    else:
        code = request.args.get('code', None)
        print code

        if code:
            try:
                o = WeChatOAuth(APPID, APP_SECRET,url)
                url = o.authorize_url

                access_token = ''
                openid = ''
                token = o.fetch_access_token(code)
                #print token
                access_token = token['access_token']
                openid = token['openid']
                print access_token
                print openid
            except Exception as e:
                print e
        else:
            uri = quote((url.decode('utf')).encode('utf-8'))
            return redirect(WX_URL + '&redirect_uri=%s&response_type=code&scope=snsapi_userinfo&state=ranker#wechat_redirect' % uri)

        if access_token!='' and openid!='':
            user_info = o.get_user_info(openid,access_token)
            if user_info:
                session['username'] = user_info['nickname']
                session['openid'] = user_info['openid']
                session['headimgurl'] = user_info['headimgurl']
                session['country'] = user_info['country']
                session['province'] = user_info['province']
                session['city'] = user_info['city']

                rs = user.get_user_info(session['openid'])
                if rs:
                    user_info['gender'] = rs['gender']
                    user_info['birthday'] = rs['birthday']
        data = {'user_info': user_info}
        return render_template('reg.htm', data=data)

@app.route('/project_info')
def project_info():
    param = request.args.get('project_name', None)
    return render_template('project_info.htm',data=param)

@app.route('/showme')
def showme():
    url = request.url.replace(request.host,HOST)
    uri = quote((url.decode('utf')).encode('utf-8'))
    user_info = get_source_userinfo(url)
    db = DBer()
    user = User()
    data = {}

    if user_info==False and not session.get('openid',False):
        #print uri
        return redirect(WX_URL + '&redirect_uri=%s&response_type=code&scope=snsapi_userinfo&state=showme#wechat_redirect' % uri)
    else:
        user_info = user.get_user_info(session.get('openid'))

    plan_info = user.get_last_planflow(session.get('openid'))
    vdot_info = user.get_last_vdot(session.get('openid'))

    if plan_info and plan_info['plan_info']:
        g_plan = json.loads(plan_info['plan_info'])
        totalweek = len(g_plan)
        firstday = g_plan[0]['detail'][0]['date']
        plan_info['totalweek'] = totalweek
        plan_info['firstday'] = firstday
        sql = """SELECT COUNT(finish_status=2 OR NULL) AS keepdays FROM t_training_plan
                 WHERE plan_id=%s""" % plan_info['id']
        rs = db.get_row(sql)

        plan_info['keepdays'] = str(rs['keepdays'])
        #判断是否完成一项训练里程碑
        finish_date = json.loads(plan_info['plan_info'])[-1]['detail'][-1]['date']
        totalkeepdays=int((plan_info['end_date']-plan_info['start_date']).days)
        keepratio = round(float(plan_info['keepdays']) / float(totalkeepdays),2) * 100
        if str(finish_date)==str(datetime.today().strftime('%Y-%m-%d')) and keepratio>=80:
            plan_info['trainingdays'] = 'bingo'
        else:
            sql1 = """SELECT COUNT(finish_status=2 OR NULL) AS trainingdays FROM t_training_plan
                     WHERE user_id=%s""" % user_info['id']
            rs1 = db.get_row(sql1)
            plan_info['trainingdays'] = str(rs1['trainingdays'])
            if plan_info['push_status'] == 1:
                plan_info['push_class1_name']="open1"
                plan_info['push_class2_name']="open2"
            else:
                plan_info['push_class1_name']="close1"
                plan_info['push_class2_name']="close2"
    else:
        plan_info = {}
        firstday = ''

    if vdot_info and vdot_info['vdot_info']:
        g_vdot = json.loads(vdot_info['vdot_info'])
        plan_info['vdots'] = g_vdot.get('vdots')

        if user_info['gender'] and vdot_info['project']:
            if user_info['gender']==1:gender='男'
            elif user_info['gender']==2:gender='女'
            ranker = user.get_my_rank(PROJECT_NAME[vdot_info['project']],gender,vdot_info['time'])
            if ranker:
                plan_info['ranker'] = ranker
            else:
                plan_info['ranker'] = 1


    if user_info['birthday']:
        user_info['age'] = datetime.today().year-int(datetime.strptime(str(user_info['birthday']),'%Y-%m-%d').year)
    if firstday!='':
        # 计算最终日期距离现在时间长度
        s_date = datetime.strptime(str(firstday),'%Y-%m-%d')
        r_date = datetime.strptime(str(plan_info['race_date']),'%Y-%m-%d')
        t_date = datetime.today()
        plan_info['remaindays'] = str((r_date - t_date).days) + ' 天'
        plan_info['progress'] = str(int(round(int(plan_info['keepdays']) * 1.0  / (r_date - s_date).days * 1.0,2) * 100)) +"%"
        plan_info['milestone'] = 'icon_milestone_10.png'


    data = {'user_info':user_info,'plan_info':plan_info}
    # print data
    return render_template('show.htm' , data = data)


@app.route('/showme/pushstatus', methods=['POST'])
def pushstatus():
    openid = session['openid']
    if openid:
        pushstatus = request.form.get('pushstatus', None)
        if str(pushstatus[0:5]) == 'close':
            pushstatus = 0
        else:
            pushstatus = 1
        user = User()
        push = user.update_push_status(openid,pushstatus)
    return ''


@app.route('/baikelist')
def baikelist():
    db = DBer()
    data = {}
    user = User()
    baike_list = user.get_baike_list(0,0)
    if baike_list:
        data = {'dlist':baike_list,'pagetitle':'马拉松百科','type':'list'}
    return render_template('baikelist.html' , data = data)

@app.route('/baikelist/top')
def top():
    db = DBer()
    data = {}
    user = User()
    baike_list = user.get_baike_list(1,0)
    if baike_list:
        data = {'dlist':baike_list,'pagetitle':'马拉松百科','type':'top'}
    return render_template('baikelist.html' , data = data)

@app.route('/baikelist/<int:id>')
def show(id):
    db = DBer()
    data = {}
    user = User()
    baike_list = user.get_baike_list(0,id)
    # print baike_list
    if baike_list:
        data = {'dlist':baike_list,'pagetitle':'马拉松百科','type':'show'}
    return render_template('baikelist.html' , data = data)


@app.route('/vdot', methods=['GET', 'POST'])
def vdot():
    url = request.url.replace(request.host,HOST)
    user_info = get_source_userinfo(url)

    uri = quote((url.decode('utf')).encode('utf-8'))
    if user_info==False and not session.get('openid',False):
        return redirect(WX_URL + '&redirect_uri=%s&response_type=code&scope=snsapi_userinfo&state=ranker#wechat_redirect' % uri)
    user_data = {}
    user_info=False
    user=User()
    openid = session.get('openid')
    user_info=user.get_user_info(openid)
    if user_info['birthday']==None:
        return redirect('/reg')
    hour=[]
    min=[]
    second=[]
    t_hour=[]
    t_min=[]
    t_second=[]
    project=[]
    vdot=user.get_last_vdot(openid)
    if  vdot:
        project=vdot['project']
        time=str(vdot['time'])
        t_hour="%02d" %int(time.split(':')[0])
        t_min="%02d" %int(time.split(':')[1])
        t_second="%02d" %int(time.split(':')[2])
    for i in range(60):
        i = "%02d" % i
        min.append(i)
        second.append(i)
    for i in range(12):
        i = "%02d" % i
        hour.append(i)
    data={"hour":hour,"min":min,"second":second,"t_hour":t_hour,"t_min":t_min,"t_second":t_second,"project":project,"user_info":user_info}
    # print data
    return render_template("vdot.html",data=data)

@app.route('/vdotresult', methods=['GET', 'POST'])
def vdot_result():
    url = request.url.replace(request.host,HOST)
    user_info = get_source_userinfo(url)

    uri = quote((url.decode('utf')).encode('utf-8'))
    if user_info==False and not session.get('openid',False):
        return redirect(WX_URL + '&redirect_uri=%s&response_type=code&scope=snsapi_userinfo&state=ranker#wechat_redirect' % uri)
    user=User()
    util=Util()
    ihours = []
    iminutes = []
    iseconds = []
    t_hour='00'
    t_minute='00'
    t_second='00'
    ihms = ['00','00','00']
    race_date = datetime.strftime(datetime.today()+timedelta(days=42),'%Y-%m-%d')
    for i in range(0,60):
        i = util.prezero(str(i),2)
        iminutes.append(i)
        iseconds.append(i)
    for i in range(0,7):
        i = util.prezero(str(i),2)
        ihours.append(i)

    if request.method=='POST':
        user_info=False
        openid = session.get('openid')
        user_info=user.get_user_info(openid)
        name=user_info['name']
        gender=user_info['gender']
        age=user_info['birthday']
        birthday=str(age).split('-')
        nowday=str( time.strftime("%Y-%m-%d", time.localtime())).split('-')
        nowdate = datetime.now().strftime('%Y-%m-%d')
        project = request.form.get('project', None).encode('utf-8',errors='ignore')
        times =  request.form.get('hour', None)+':'+ request.form.get('minute', None)+ ':'+ request.form.get('second', None)
        hour = request.form.get('hour', None)
        minute = request.form.get('minute',None)
        second = request.form.get('second',None)
        planflow=user.get_last_planflow(openid)
        if planflow:
            t_time=str(planflow['t_times'])
            t_hour="%02d" %int(t_time.split(':')[0])
            t_minute="%02d" %int(t_time.split(':')[1])
            t_second="%02d" %int(t_time.split(':')[2])
        tp=util.get_kms(project)
        vdot_info= util.get_vdot(tp,times)
        vdots= json.loads(vdot_info)['vdots']
        age=int(nowday[0])-int(birthday[0])+1
        target_dist=lable(vdots,age,gender)
        if gender==1:
            gender_type=sex_man
            sex_zc='男'
        else :
            gender_type=sex_woman
            sex_zc='女'
        num=user.get_my_rank(project,sex_zc,times)+1
        now_time=time.strftime("%Y-%m-%d ",time.localtime(time.time()))
        # print "game---------------------",game
        user.apdate_user_target(target_dist,open)
        user.add_update_vdot(name,openid,project,times,vdot_info)
        url='http://x.lares.me/vdotresult'
        wx = util.get_wx_shareinfo(url)
        data={'num':num,'target_dist':target_dist,'vdots':vdots,'wx':wx,'openid':openid,'project':project,'hour':hour,'minute':minute,'second':second,'ihours':ihours,'iminutes':iminutes,'iseconds':iseconds,'ihms':ihms,'race_date':race_date,'times':times,'t_hour':t_hour,'t_minute':t_minute,'t_second':t_second,'nowdate':nowdate}
        session['vdot_info']=vdot_info
        session['vdots']=vdots
        plan = user.get_last_planflow(session['openid'])
        if plan != False:
            data['haveplan'] = 1
        else:
            data['haveplan'] = 0
        # print data
        return render_template("vdot_result.html",data=data)

@app.route('/vdot_show')
def vdot_show():
    user=User()
    util=Util()
    #openid = 'oPd_yvvezsawVvaoXnWhkJZvSuOg'
    openid=request.args.get('openid')
    if openid==None:
        return redirect('/')
    # print openid
    meg=user.get_last_vdot(openid)

    target_dist=user.get_user_info(openid)
    gender=target_dist['gender']
    if gender==1:
        gender_type=sex_man
        sex_zc='男'
    else :
        gender_type=sex_woman
        sez_zc='女'
    project=meg['project']
    times=meg['time']
    # print '1233',times
    num=user.get_my_rank(project,sex_zc,times)+1
    # print num

    # print meg
    vdots=meg['vdot_info']
    target_dist= target_dist['target_dist']
    # print target_dist
    # print  json.loads(vdots)
    data={'num':num,'target_dist':json.loads(target_dist),'vdots':json.loads(vdots)['vdots'],'openid':openid}
    return render_template("vdot_show.html",data=data)

if __name__ == '__main__':
    app.run('0.0.0.0', 80, debug=True)
