import sys
import os
import hashlib
import time
import datetime

try:
    if os.environ.get('HTTP_AUTHORIZATION') != 'Basic xxx':
        raise Exception('wrong pass')
    
    clen = int(os.environ.get('CONTENT_LENGTH', 0))
    if clen:
        data = sys.stdin.read(clen)
        if len(data) == clen:
            fnz = "%s_%s.jpg" % (datetime.datetime.now().strftime('%y/%m/%d_%H%M%S'),
                                  hashlib.md5( os.urandom(256) + str(time.time()) ).hexdigest()
            )
            ffnz = os.path.realpath("../img/" + fnz)
            try:
                os.makedirs(os.path.dirname(ffnz))
            except:
                pass
            open(ffnz, 'wb').write(data)
            print "http://img.dev99.net/" + fnz
except:
    pass

