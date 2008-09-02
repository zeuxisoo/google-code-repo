# -*- coding: utf8 -*-

import re

from math import ceil

def htmlSpecialChars(s):
	s = re.sub(r'&',  r'&amp;', s)
	s = re.sub(r'<',  r'&lt;', s)
	s = re.sub(r'>',  r'&gt;', s)
	s = re.sub(r'"',  r'&quot;', s)
	s = re.sub(r'\'', r'&apos;', s)
	return s;

def breakLine(s, t = 'ENCODE'):
	if t == 'ENCODE':
		s = s.replace('\r\n', '\\r\\n')
		s = s.replace('\r', '\\r')
		s = s.replace('\n', '\\n')
	else:
		s = s.replace('\\r\\n', '\r\n')
		s = s.replace('\\r', '\r')
		s = s.replace('\\n', '\n')
	return s

def clearSplitCmd(s):
	return s.replace('<>', '&lt;&gt;')

def nl2br(s):
	s = breakLine(s, 'DECODE')
	s = s.replace('\r\n', '<br />')
	s = s.replace('\r', '<br />')
	s = s.replace('\n', '<br />')
	return s

def multiPage(num, perpage, curpage, mpurl):
	multipage = ''

	if str(mpurl).rfind("?") != -1:
		mpurl = '&amp;'
	else:
		mpurl = '?'

	if num > perpage:
		page = 9
		offset = 4
		pages = ceil(float(num) / perpage)
		curpage = int(curpage)
	
		if page > pages:
			start = 1
			to = pages
		else:
			start = curpage - offset
			to = curpage + page - offset - 1

			if start < 1:
			
				to = curpage + 1 - start
				start = 1
				
				if (to - start) < page and (to - start) < pages:
					to = page

			elif to > pages:

				start = curpage - pages + to
				to = pages

				if (to - start) < page and (to - start) < pages:
					start = pages - page + 1

		if (curpage - offset > 1 and pages > page):
			multipage = '<a href="' + mpurl + 'page=1" class="p_redirect">&laquo;</a>'
		else:
			multipage = ''

		if (curpage > 1):
			multipage += '<a href="' + mpurl + 'page=' + str(curpage - 1) + '" class="p_redirect">‹</a>'
		else:
			multipage += ''

		for i in range(start, to):
			if (i == curpage):
				multipage += '<span class="p_curpage">' + str(i) + '</span>'
			else:
				multipage += '<a href="' + mpurl + 'page=' + str(i) + '" class="p_num">' + str(i) + '</a>'
			

		if (curpage < pages):
			multipage += '<a href="' + mpurl + 'page=' + str(curpage + 1) + '" class="p_redirect">›</a>'
		else:
			multipage += ''
		
		if (to < pages):
			multipage += '<a href="' + mpurl + 'page=' + str(pages) + '" class="p_redirect">&raquo;</a>'
		else:
			multipage += ''

		if multipage:
			multipage = '<div class="p_bar"><span class="p_info">Records:' + str(num) + '</span>' + multipage + '</div>'
		else:
			multipage = ''

	return multipage