# -*- coding: utf8 -*-

class configure(object):
	def __init__(self):
		self.bookTitle = "派仿留言板"

		self.datMessage = "database/message.txt"
		self.idxMessage = "database/message.idx"

		self.genderName = {
							"1" : "男生",
							"2" : "女生"
						  }
		
		self.genderColor= {
							"1" : "#6666FF",
							"2" : "#FF33FF"
						  }

		self.dateFormat = "%Y-%m-%d %H:%M:%S (%a)"
		self.timeZone = 0
		self.showPage = 1