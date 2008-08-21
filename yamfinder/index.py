#!/usr/bin/env python
# -*- coding: utf-8 -*-

# Import Self Defined Program Method
import finder

# Import Google API
from google.appengine.ext import webapp
from google.appengine.ext.webapp.util import run_wsgi_app
from google.appengine.ext.webapp import template

# Load Filter File
template.register_template_library('common.filter.dateformat')

# Main Handler
def main():
	# |޾
	application = webapp.WSGIApplication(
										 [
											('/', finder.PageIndex),
											('/get-mp3', finder.PageGetMP3),
											('/get-mp3-list', finder.PageGetMP3List),
											('/searh-mp3-id', finder.PageSearchMP3Id)
										 ],
										 debug=True
										 )
	run_wsgi_app(application)

#
if __name__ == "__main__":
	main()