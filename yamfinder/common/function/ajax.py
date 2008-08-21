from django.utils import simplejson
from django.conf import settings

def json(data):
    encode = settings.DEFAULT_CHARSET
    return simplejson.dumps(uni_str(data, encode))

def uni_str(a, encoding):
    if isinstance(a, (list, tuple)):
        s = []
        for i, k in enumerate(a):
            s.append(uni_str(k, encoding))
        return s
    elif isinstance(a, dict):
        s = {}
        for i, k in enumerate(a.items()):
            key, value = k
            s[uni_str(key, encoding)] = uni_str(value, encoding)
        return s
#    elif isinstance(a, str):
#        return unicode(a, encoding)
    elif isinstance(a, str) or (hasattr(a, '__str__') and callable(getattr(a, '__str__'))):
        if getattr(a, '__str__'):
            a = str(a)
        return unicode(a, encoding)
    elif isinstance(a, unicode):
        return a
    else:
        return a