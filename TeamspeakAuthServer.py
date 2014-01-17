# -*- coding: utf-8 -*-
import random
import MySQLdb
import json
from authserver import AuthServer

HOST = "publicuhc.com"
PORT = 35879
MOTD = u"§epublicuhc Auth Server"
FAVICON = 'publicuhc.png'  # or a path to a 64x64 .png


class TeamspeakAuthServer(AuthServer):
    def handle_auth(self, client_addr, server_addr, username, authed):
        print "%s/%s logged in" % (username, client_addr)
        if authed:
            print " --> OK!"
            with open('config.json') as data_file:
                data = json.load(data_file)
            db = MySQLdb.connect(host=data['db']['host'],port=data['db']['port'], user=data['db']['username'], passwd=data['db']['password'], db=data['db']['database'])
            cur = db.cursor()
            code = ''.join(random.choice('ABCDEF0123456789') for i in range(10))
            print " --> CODE "+code
            cur.execute("INSERT INTO auth_codes(mc_name,auth_code) "
                        "VALUES (%s,%s) "
                        "ON DUPLICATE KEY UPDATE "
                        "auth_code=%s,created_time=NOW()", (username, code, code))
            cur.close()
            db.commit()
            db.close()
            return "Your code is \""+code+"\", it will last for 15 minutes."
        else:
            print " --> Failed login"
            return u"§4Couldn't authenticate you, please try again later"


if __name__ == "__main__":
    server = TeamspeakAuthServer(MOTD, FAVICON)
    server.listen(HOST, PORT)
    server.run()