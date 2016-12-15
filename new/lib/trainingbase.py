# -*- coding: utf-8 -*-
import sys
from etc.setting import *
from util import *

class TrainingBase(object):
	"""训练基类"""
	def __init__(self):
		pass


class TrainingItem():
    def __init__(self, itemName):
        self.itemName = itemName

    def __str__(self):
        return self.itemName
# 休息
class Rest(TrainingItem):
    def __init__(self):
        TrainingItem.__init__(self,"0")
        self.fName = '休息'

    def get_dict(self):
    	return {'name':self.itemName,'fName':self.fName}

# E 代表轻松跑（Easy running）
class EasyRun(TrainingItem):
    def __init__(self, minDist, maxDist, baseDist=3):
        TrainingItem.__init__(self,"E")
        self.minDist = minDist
        self.maxDist = maxDist
        self.baseDist = baseDist
        self.fName = '轻松跑'

    def __str__(self):
        return "{0} ({1}~{2}km)".format(self.itemName, self.minDist, self.maxDist)

    def get_average_dist(self):
    	return (self.minDist + self.maxDist) / 2

    def get_dict(self):
    	return {'name':self.itemName,'dist':self.get_average_dist(),'fName':self.fName}

# M代表马拉松配速跑（Marathon-pacerunning）
class MarathonRun(TrainingItem):
    def __init__(self, minutes):
        TrainingItem.__init__(self,"M")
        self.minutes = int(minutes)
        self.fName = '马拉松配送跑'

    def __str__(self):
        return "{0} ({1} mins)".format(self.itemName, self.minutes)

    def get_dict(self):
    	return {'name':self.itemName,'mins':self.minutes,'fName':self.fName}

# T代表乳酸门槛跑（Thresholdrunning）
class TempoRun(TrainingItem):
    def __init__(self, minutes):
        TrainingItem.__init__(self,"T")
        self.minutes = int(minutes)
        self.fName = '乳酸门槛跑'

    def __str__(self):
        return "{0} ({1} mins)".format(self.itemName, self.minutes)

    def get_dict(self):
    	return {'name':self.itemName,'mins':self.minutes,'fName':self.fName}

# I代表间歇训练（Intervaltraining）
class IntervalRun(TrainingItem):
    def __init__(self, repeatTimes, repeatDistance):
        TrainingItem.__init__(self,"I")
        self.repeatTimes = repeatTimes
        self.repeatDistance = repeatDistance
        self.fName = '间歇训练'

    def __str__(self):
        return "{0} ({1}x{2})".format(self.itemName, self.repeatDistance, self.repeatTimes)

    def get_dict(self):
    	return {'name':self.itemName,'repeatDistance':self.repeatDistance,'repeatTimes':self.repeatTimes,'fName':self.fName}

# R代表重复训练（Repetition training）
class RepRun(TrainingItem):
    def __init__(self, itemName, repeatTimes, repeatDistance):
        TrainingItem.__init__(self,"R")
        self.repeatTimes = repeatTimes
        self.repeatDistance = repeatDistance
        self.remark = 'at least 5mins recovery'
        self.fName = '重复训练'

    def __str__(self):
        return "{0}  ({1}x{2})".format(self.itemName, self.repeatDistance, self.repeatTimes)

    def get_dict(self):
    	return {'name':self.itemName,'repeatDistance':self.repeatDistance,'repeatTimes':self.repeatTimes,'fName':self.fName}

class LSD(TrainingItem):
    def __init__(self, dist):
        TrainingItem.__init__(self,"L")
        self.dist = dist
        self.fName = '长距离慢跑'

    def __str__(self):
        return "{0} ({1}km)".format(self.itemName, self.dist)

    def get_dict(self):
    	return {'name':self.itemName,'dist':self.dist,'fName':self.fName}


class RaceDay(TrainingItem):
    def __init__(self):
        TrainingItem.__init__(self,"X")
        self.fName = '比赛日'

    def __str__(self):
        return "{0}".format(self.itemName)

    def get_dict(self):
    	return {'name':self.itemName,'fName':self.fName}


class TrainingPlanGenerator:

    def __init__(self, totalWeeks=12, warmUpWeeks=2, coolDownWeeks=2, lsdRestInterval=3,
                 minEasyRunDist=5, maxEasyRunDist=8,
                 minPaceRunMinutes=45, maxPaceRunMinutes=60,
                 minTempoRunMinutes=20, maxTempoRunMinutes=35,
                 minYassoTimes=5, maxYassoTimes=8,
                 minLsdDist=15, maxLsdDist=35,raceDate=''):
        self.totalWeeks = totalWeeks
        self.warmUpWeeks = warmUpWeeks
        self.coolDownWeeks = coolDownWeeks
        self.lsdRestInterval = lsdRestInterval

        self.minEasyRunDist = minEasyRunDist
        self.maxEasyRunDist = maxEasyRunDist
        self.minPaceRunMinutes = minPaceRunMinutes
        self.maxPaceRunMinutes = maxPaceRunMinutes
        self.minTempoRunMinutes = minTempoRunMinutes
        self.maxTempoRunMinutes = maxTempoRunMinutes
        self.minYassoTimes = minYassoTimes
        self.maxYassoTimes = maxYassoTimes
        self.minLsdDist = minLsdDist
        self.maxLsdDist = maxLsdDist
        self.raceDate = raceDate
        # 默认是周日比赛日
        self.raceday = 6
        self.util = Util()
        if self.raceDate!='':
        	self.raceday = self.util.get_weekdaynum_of_date(self.raceDate)
        	_racedate = datetime.strptime(self.raceDate,'%Y-%m-%d')
        	_raceweek = int(time.strftime("%W",_racedate.timetuple()))
        	_currweek = int(time.strftime("%W"))
        	_raceyear = int(time.strftime("%Y",_racedate.timetuple()))
        	_curryear = int(time.strftime("%Y",datetime.now().timetuple()))

        	self.totalWeeks = (_raceyear-_curryear)*52+_raceweek-_currweek+1


        self.plan = {"1": [], "2": [], "3": [],
                     "4": [], "5": [], "6": [], "7": []}

    def get_weeks_of_dayX(self,dayX):
    	weeks = self.totalWeeks
    	# print "self.raceday:%s" % self.raceday
    	if self.raceday==dayX:
    		weeks=weeks-1
        if weeks == 1:
            weeks = 2
    	# print 'weeks:%d' % weeks

    	return weeks

    def generatePlan(self):
        self.generatePlanForDay1()
        self.generatePlanForDay2()
        self.generatePlanForDay3()
        self.generatePlanForDay4()
        self.generatePlanForDay5()
        self.generatePlanForDay6()
        self.generatePlanForDay7()

    def generatePlanForDay1(self):
    	weeks = self.get_weeks_of_dayX(0)
        for i in range(weeks):
        	_r = Rest()
        	self.plan["1"].append(_r.get_dict())
        if self.totalWeeks-weeks>0:
        	_r = RaceDay()
        	self.plan["1"].append(_r.get_dict())

    def generatePlanForDay2(self):
    	weeks = self.get_weeks_of_dayX(1)
        # warm up weeks : tempo run (incrementally)
        increaseRatio = (
            self.maxTempoRunMinutes*1.0 / self.minTempoRunMinutes) ** (1.0 / (self.warmUpWeeks - 1))
        for i in range(self.warmUpWeeks):
            tempoMinutes = round(self.minTempoRunMinutes * increaseRatio ** i)
            _t = TempoRun(tempoMinutes)

            self.plan["2"].append(_t.get_dict())

        # between warmup weeks and cooldown weeks : yasso 1000 (incrementally)
        yassoWeeks = weeks - self.warmUpWeeks - self.coolDownWeeks
        averageWeeks = yassoWeeks // (self.maxYassoTimes -
                                      self.minYassoTimes + 1)
        remainingWeeks = yassoWeeks % (
            self.maxYassoTimes - self.minYassoTimes + 1)
        for times in range(self.minYassoTimes, self.maxYassoTimes + 1):
            for i in range(averageWeeks):
            	_i = IntervalRun(times,800)
                self.plan["2"].append(_i.get_dict())
            if remainingWeeks > 0:
                _i = IntervalRun(times,1000)
                self.plan["2"].append(_i.get_dict())
                remainingWeeks -= 1

        # cool down weeks (except last week): tempo run (decrementally)
        decreaseRatio = (
            self.minTempoRunMinutes*1.0 / self.maxTempoRunMinutes) ** (1.0 / (self.coolDownWeeks - 1))
        for i in range(1, self.coolDownWeeks):
            tempoMinutes = round(self.maxTempoRunMinutes * decreaseRatio ** i)
            _t = TempoRun(tempoMinutes)
            self.plan["2"].append(_t.get_dict())

        # last week : easy run
        _e = EasyRun(self.minEasyRunDist, self.maxEasyRunDist)
        self.plan["2"].append(_e.get_dict())

        if self.totalWeeks-weeks>0:
        	_r = RaceDay()
        	self.plan["2"].append(_r.get_dict())

    def generatePlanForDay3(self):
    	weeks = self.get_weeks_of_dayX(2)
        # before last week : easy run
        increaseRatio =  (self.maxEasyRunDist*1.0 / self.minEasyRunDist)** (1.0 / (weeks-1))
        # print 'generatePlanForDay3'
        # print increaseRatio
        for i in range(weeks - 1):
        	_e = EasyRun(round(self.minEasyRunDist*increaseRatio**i), round(self.maxEasyRunDist*increaseRatio**i))
        	self.plan["3"].append(_e.get_dict())

        # last week : rest
        _r = Rest()
        self.plan["3"].append(_r.get_dict())
        if self.totalWeeks-weeks>0:
        	_r = RaceDay()
        	self.plan["3"].append(_r.get_dict())

    def generatePlanForDay4(self):
    	weeks = self.get_weeks_of_dayX(3)
        # warm up weeks : pace run (incrementally)
        increaseRatio = (
            self.maxPaceRunMinutes*1.0 / self.minPaceRunMinutes) ** (1.0 / (self.warmUpWeeks))
        for i in range(self.warmUpWeeks):
            paceRunMinutes = round(self.minPaceRunMinutes * increaseRatio ** i)
            _m = MarathonRun(paceRunMinutes)
            self.plan["4"].append(_m.get_dict())

        # between warm up weeks and cool down weeks : pace run (max minutes)
        for i in range(weeks - self.warmUpWeeks - self.coolDownWeeks):
        	_m = MarathonRun(self.maxPaceRunMinutes)
        	self.plan["4"].append(_m.get_dict())

        # cool down weeks (except last week) : pace run (decrementally)
        decreaseRatio = (
            self.minPaceRunMinutes*1.0 / self.maxPaceRunMinutes) ** (1.0 / (self.coolDownWeeks - 1))
        for i in range(1, self.coolDownWeeks):
            paceRunMinutes = round(self.maxPaceRunMinutes * decreaseRatio ** i)
            _m = MarathonRun(paceRunMinutes)
            self.plan["4"].append(_m.get_dict())

        # last week : easy run
        _e = EasyRun(self.minEasyRunDist, self.maxEasyRunDist)
        self.plan["4"].append(_e.get_dict())
        if self.totalWeeks-weeks>0:
        	_r = RaceDay()
        	self.plan["4"].append(_r.get_dict())


    def generatePlanForDay5(self):
    	weeks = self.get_weeks_of_dayX(4)
        for i in range(weeks):
        	_r = Rest()
        	self.plan["5"].append(_r.get_dict())
        if self.totalWeeks-weeks>0:
        	_r = RaceDay()
        	self.plan["5"].append(_r.get_dict())

    def generatePlanForDay6(self):
    	weeks = self.get_weeks_of_dayX(5)
        # before cool down weeks : lsd (incrementally)
        lsdWeeks = weeks - self.coolDownWeeks - \
            ((weeks - self.coolDownWeeks) // self.lsdRestInterval)
        # print '=============lsdWeeks=============='
        xw = lsdWeeks - 1
        if xw == 0:
            xw = 1
        # print self.maxLsdDist*1.0 / self.minLsdDist
        increaseRatio = (self.maxLsdDist*1.0 / self.minLsdDist) ** (1.0 / xw)

        # print increaseRatio

        seq = 0
        for week in range(1, weeks - self.coolDownWeeks + 1):
            if (week % self.lsdRestInterval == 0):
            	_r = Rest()
            	self.plan["6"].append(_r.get_dict())
            else:
                dist = round(self.minLsdDist * increaseRatio ** seq)
                _l = LSD(dist)
                self.plan["6"].append(_l.get_dict())
                seq += 1

        # cool down weeks (except last week) : lsd (decrementally)
        decreaseRatio = (
            self.minLsdDist*1.0 / self.maxLsdDist) ** (1.0 / (self.coolDownWeeks - 1))
       	# print decreaseRatio
        for i in range(self.coolDownWeeks - 1):
            dist = round(self.maxLsdDist * decreaseRatio ** (i + 1))
            _l = LSD(dist)
            self.plan["6"].append(_l.get_dict())

        # last week : rest
        _r = Rest()
        self.plan["6"].append(_r.get_dict())
        if self.totalWeeks-weeks>0:
        	_r = RaceDay()
        	self.plan["6"].append(_r.get_dict())

    def generatePlanForDay7(self):
    	weeks = self.get_weeks_of_dayX(6)
        for i in range(weeks):
        	_e = EasyRun(self.minEasyRunDist, self.maxEasyRunDist)
        	self.plan["7"].append(_e.get_dict())

        if self.totalWeeks-weeks>0:
        	_r = RaceDay()
        	self.plan["7"].append(_r.get_dict())

    def getTrainingPlan(self):
        plans = []
        # print("\n全程马拉松训练计划（{0} weeks）：\n".format(self.totalWeeks))
        # print("{0:10s}{1:7s}{2:22s}{3:19s}{4:21s}{5:7s}{6:13s}{7:16s}".format(
        #     "", "1", "2", "3", "4", "5", "6", "7"))
        # print("-" * 115)
        dayOfWeek = datetime.today().weekday()
        firstDate = datetime.today() + timedelta(days=-dayOfWeek)



        for i in range(self.totalWeeks):
            weekSeq = str(i + 1)
            items = []
            weekplan = []
            week_dist = 0
            for day in sorted(self.plan.keys()):
                d = (firstDate+timedelta(int(day)-1+i*7)).strftime('%Y-%m-%d')
                items.append(self.plan[day][i])
                if self.plan[day][i].get('dist'):
                	week_dist+= int(self.plan[day][i]['dist'])
                # if self.plan[day][i].get('mins'):
                # 	pace_info = self.plan[day][i].get('pace')
                # 	if pace_info:
                # 		print '||||||||||||||||||||||||||||||||||'
                # 		print pace_info
                # 		pace_info = pace_info.split(':')
	               #  	t = datetime.timedelta(minutes=pace_info[0],seconds=pace_info[1])
	               #  	week_dist+= int(self.plan[day][i]['mins']) * 60.0 / t.seconds
                weekplan.append({'date':d,'item':self.plan[day][i]})
            plans.append({'week':weekSeq,'detail':weekplan,'week_dist':week_dist})
            # print("{0:10s}{1:7s}{2:22s}{3:19s}{4:21s}{5:7s}{6:13s}{7:16s}".format(
            #     weekSeq, items[0]['name'], items[1]['name'], items[2]['name'], items[3]['name'], items[4]['name'], items[5]['name'], items[6]['name']))

        return plans

if __name__ == "__main__":
	generator = TrainingPlanGenerator(totalWeeks=6)
	generator.generatePlan()
	plan = generator.getTrainingPlan()
	# print plan
