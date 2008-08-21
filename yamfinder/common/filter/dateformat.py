# -*- coding: utf-8 -*-
# import the webapp module
from google.appengine.ext import webapp

# import the python module
from datetime import timedelta

# 建立註冊變量, 之後需要用他去註冊自定義的過濾函式
register = webapp.template.create_template_register() 

# 時間格式化轉換
@register.filter
def dateFormat(value):
	return (value + timedelta(hours=+8)).strftime("%Y-%m-%d %H:%M:%S")

#register.filter(dateFormat)