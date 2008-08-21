#!/usr/bin/env python
# -*- coding: utf-8 -*-

import sys, cgi, os, urllib, re
import json

# Add Dir "models, common/function" To System Path -> For Program Call It
sys.path.append('models')
sys.path.append('common/function')

# Form "google" Import "urlfetch, webapp, template, db"
from google.appengine.api import urlfetch
from google.appengine.ext import webapp
from google.appengine.ext.webapp import template
from google.appengine.ext import db

# Import Paginator
from django.core.paginator import ObjectPaginator, InvalidPage

# Form "models" Import "DataYamMP3URL" Detail
from models import DataYamMP3URL


# Page -> Index
class PageIndex(webapp.RequestHandler):
	def get(self):
		# Self Define Template Filter Method
		#webapp.template.register_template_library('common.templatefilters')

		# OutPut Type Of Site + Language
		self.response.headers['Content-Type'] = 'text/html; charset=big5'
		
		# From DataStore Get Data
		#yamMP3Datas = db.GqlQuery("SELECT * FROM DataYamMP3URL ORDER BY date DESC LIMIT 10")
		yamMP3Datas = DataYamMP3URL.gql("ORDER BY date DESC LIMIT 10")

		templateVar = { 'yamMP3Total' : yamMP3Datas.count() }

		"""
		yamMP3Datas=DataYamMP3URL.all()
		yamMP3Datas.order('-date')
		yamMP3Datas=yamMP3Datas.fetch(limit=100)
		showPage = 1
		paginator = ObjectPaginator(yamMP3Datas,  showPage)

		try:
			page = int(self.request.get('page', 0))
			visits = paginator.get_page(page)
		except InvalidPage:
			raise http.Http404

		templateVar = {
			'visits': visits,
			'is_paginated' : True,
			'results_per_page' : showPage,
			'has_next': paginator.has_next_page(page),
			'has_previous': paginator.has_previous_page(page),
			'page': page + 1,
			'next': page + 1,
			'previous': page - 1,
			'pages': paginator.pages,
			'yamMP3Total' : DataYamMP3URL.gql("ORDER BY date DESC LIMIT 10").count()
		}
		"""

		# Render Template + OutPut
		path = os.path.join(os.path.dirname(__file__), 'template/index.html')
		self.response.out.write(template.render(path, templateVar))


# Page -> GetMP3
class PageGetMP3(webapp.RequestHandler):
	def checkURL(self, url):
		return re.match('^http:\/\/mymedia\.yam\.com\/m\/([0-9]+)$', url)
	
	def getId(self, url):
		return re.compile(r'^http:\/\/mymedia\.yam\.com\/m\/([0-9]+)$').match(url).group(1)

	def getMP3(self, line):
		return re.compile(r'mp3file=(.*?)\&totaltime=[0-9]+').match(line).group(1)

	def makeMP3URL(self, ids):
		return "http://mymedia.yam.com/api/a/?pID=" + ids
		
	def getContent(self, url):
		result = urlfetch.fetch(url)
		if result.status_code == 200:
			return result.content
		else:
			return None

	def post(self):
		# Get Post Data -> URL
		url = self.request.get('url')

		# Check URL
		if not self.checkURL(url):
			self.response.out.write("URL Format Invalid")

		# Get Ids + Complete URL + Get Data
		ids = self.getId(url)
		content = self.getContent(self.makeMP3URL(ids))

		if content is not None:
			rurl = self.getMP3(content)

			if DataYamMP3URL.gql("WHERE fake_ids = :f_ids", f_ids=ids).count() <= 0:
				# Open DataStore -> Save Data
				dataYamMP3URL = DataYamMP3URL()
				dataYamMP3URL.fake_ids = ids
				dataYamMP3URL.fake_url = url
				dataYamMP3URL.real_url = rurl
				dataYamMP3URL.put()
		else:
			rurl = "Error"

		self.response.out.write(rurl)


# Page -> GetMP3List
class PageGetMP3List(webapp.RequestHandler):
	def post(self):
		dataArr = []
		yamMP3Datas = db.GqlQuery("SELECT * FROM DataYamMP3URL ORDER BY date DESC LIMIT 10")

		for data in yamMP3Datas:
			dataArr.append(data)

		output = { 'myData' : dataArr }

		self.response.headers['Content-Type'] = 'text/plain'
		self.response.out.write(json.encode(output))

# Page -> SearchMP3Id
class PageSearchMP3Id(webapp.RequestHandler):
	def post(self):
		# Set OutPut Type
		self.response.headers['Content-Type'] = 'text/plain'

		# Get Id
		keyword = self.request.get('keyword')
		
		# Make Data Struct
		dataArr = []
		yamMP3Datas = db.GqlQuery("SELECT * FROM DataYamMP3URL WHERE fake_ids = :1 ORDER BY fake_ids, date DESC LIMIT 10", keyword)

		for data in yamMP3Datas:
			dataArr.append(data)

		output = { 'success' : 'true', 'myData' : dataArr }

		self.response.out.write(json.encode(output))