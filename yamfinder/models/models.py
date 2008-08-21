from google.appengine.ext import db

# Data -> Yam URL
class DataYamMP3URL(db.Model):
	fake_ids = db.StringProperty()
	fake_url = db.StringProperty()
	real_url = db.StringProperty()
	date= db.DateTimeProperty(auto_now_add=True)