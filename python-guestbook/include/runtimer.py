# -*- coding: utf8 -*-

import time

class runtimer(object):

	def __init__(self):
		self.start = time.clock()

	def stop(self):
		self.end = time.clock()
		return "%.6f" % float(self.end - self.start)