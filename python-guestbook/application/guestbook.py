# -*- coding: utf8 -*-
import os, sys
import CaptchasDotNet

from functions import *
from time import time, strftime, localtime
from operator import itemgetter

class guestbook(object):
	def __init__(self, configure, template, form, runtimer):
		self.cfg = configure
		self.tpl = template
		self.f = form
		self.rt = runtimer

	def pageIndex(self):

		#
		page= self.f.getvalue('page')

		if page is not None:
			offset = (int(page) - 1) * self.cfg.showPage
			pages = int(page)
		else:
			offset = 0
			pages = 1

		#
		rMess = open(self.cfg.datMessage)
		tMess = {}

		#
		try:
			aMess = rMess.readlines(); aMess.reverse()
			pagebar = multiPage(len(aMess), self.cfg.showPage, pages, os.environ['SCRIPT_NAME'])


			for i in range(offset, int(pages * self.cfg.showPage)):
				mId, mUsername, mGender, mTitle, mMessage, mAddDate, mBreak = aMess[i].split('<>')
				tMess[mId] = {
								'username' : htmlSpecialChars(mUsername),
								'gender' : htmlSpecialChars(mGender),
								'title' : htmlSpecialChars(mTitle),
								'message' : nl2br(htmlSpecialChars(mMessage)),
								'addDate' : strftime(self.cfg.dateFormat, localtime(float(mAddDate) + self.cfg.timeZone * 3600))
							 }
		finally:
			rMess.close()

		del page, rMess, aMess

		#
		self.tpl.display('index.html', {
				'pyself' : os.environ['SCRIPT_NAME'],
				'cfg' : self.cfg,
				'mess' : tMess,
				'pagebar' : pagebar,
				'rt' : self.rt.stop()
			}
		)

	def pageAdd(self):
		if self.f.getvalue('sk') == 'add':
			
			#
			username = self.f.getvalue('username')
			gender = self.f.getvalue('gender')
			title = self.f.getvalue('title')
			message = self.f.getvalue('message')

			#
			if username == None:
				self.pageError('請輸入暱稱')
			
			if gender == None:
				self.pageError('請選擇性別')

			if title == None:
				self.pageError('請輸入標題')
			
			if message == None:
				self.pageError('請輸入內容')
			
			#
			rIdx = open(self.cfg.idxMessage, "r+")
			i = int(rIdx.readline())
			wIdx = open(self.cfg.idxMessage, "w+")
			wIdx.write(str(i+1))
			wIdx.close()
			rIdx.close()

			del rIdx, wIdx

			#
			username = clearSplitCmd(breakLine(username))
			gender = clearSplitCmd(breakLine(gender))
			title = clearSplitCmd(breakLine(title))
			message = clearSplitCmd(breakLine(message))

			#
			wMess = open(self.cfg.datMessage, "a+")
			wMess.write( "%s<>%s<>%s<>%s<>%s<>%s<>\n" % (str(i+1), username, gender, title, message, str(time())) )
			wMess.close()

			del username, gender, title, message, wMess, i

			#
			self.pageError('發表留言完成', None)

	def pageError(self, message, kind = 'ERR', url = 'index.py'):
		self.tpl.display('error.html', {
				'message' : message,
				'kind' : kind,
				'url' : url
			}
		)
		sys.exit()