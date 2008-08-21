"""
Modifier
  Zeuxis Lo
WebSite
  http://zeuik.com/
Version
  19/8/2008 21:08
Base On
  http://code.google.com/p/google-app-engine-samples/source/browse/trunk/geochat/json.py
"""

import datetime
import simplejson
import time

from datetime import timedelta

from google.appengine.api import users
from google.appengine.ext import db

class GqlEncoder(simplejson.JSONEncoder):

	def default(self, obj):

		if hasattr(obj, '__json__'):
			return getattr(obj, '__json__')()

		if isinstance(obj, db.GqlQuery):
			return list(obj)

		elif isinstance(obj, db.Model):
			properties = obj.properties().items()
			output = {}
			for field, value in properties:
				output[field] = getattr(obj, field)
			return output

		elif isinstance(obj, datetime.datetime):
			output = {}
			output['freal'] = str(obj)
			output['ftune'] = (obj + timedelta(hours=+8)).strftime("%Y-%m-%d %H:%M:%S")
			output['fsecs'] = time.mktime(obj.timetuple())
			return output

		elif isinstance(obj, time.struct_time):
			return list(obj)

		return simplejson.JSONEncoder.default(self, obj)


def encode(input):
	return GqlEncoder().encode(input)