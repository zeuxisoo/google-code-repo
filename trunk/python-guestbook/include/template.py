# -*- coding: utf8 -*-

import os, sys

from mako.template import Template
from mako.lookup import TemplateLookup

class template(object):
	def __init__(self):
		dirName = os.path.join(os.path.abspath(os.path.dirname(__file__)), '../template')

		"""
		input_encoding : 模版原來的編碼
		output_encoding: 模版輸出的編碼
		"""
		self.myLookup= TemplateLookup(directories=[dirName], default_filters=['decode.utf8'], filesystem_checks=False, format_exceptions=True, input_encoding='utf-8', output_encoding='utf-8')

	def display(self, fileName, templateVar = {}):
		try:
			print self.myLookup.get_template(fileName).render(**templateVar)
		except:
			from mako import exceptions
			print exceptions.html_error_template().render()