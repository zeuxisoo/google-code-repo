#!/usr/python
# -*- coding: utf-8 -*-

#
def main():
	#
	from runtimer import *
	rt = runtimer()

	#
	from configure import *
	from template import *
	from guestbook import *
	
	#
	gb = guestbook(configure(), template(), _f, rt)

	#
	if _f.has_key('sk'):

		result = {
			'add' : gb.pageAdd
		}[_f.getvalue('sk')]()

	else:
		gb.pageIndex()

#
if __name__ == "__main__":
	import sys, os, cgi, cgitb; cgitb.enable(); _f = cgi.FieldStorage()

	__version__ = '0.0.1'

	sys.path.append('include')
	sys.path.append('application')

	sys.stdout.write("Content-Type: text/html; charset=UTF-8\r\n\r\n")
	sys.exit(main())