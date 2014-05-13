# -*- coding: utf-8 -*-
import random
import MySQLdb
import yaml
from authserver import AuthServer

with open('./../config/config.yml') as data_file:
    data = yaml.load(data_file)

MC_HOST = data['minecraft']['host']
MC_PORT = data['minecraft']['port']
MC_MOTD = data['minecraft']['motd']
DB_HOST = data['database']['host']
DB_PORT = data['database']['port']
DB_USERNAME = data['database']['username']
DB_PASSWORD = data['database']['password']
DB_DATABASE = data['database']['database']
DB_TABLE = data['database']['minecraft_table']
FAVICON = 'servericon.png'  # path to a 64x64 .png


class TeamspeakAuthServer(AuthServer):
    def handle_auth(self, client_addr, server_addr, username, authed):
        print "%s/%s logged in" % (username, client_addr)
        if authed:
            print " --> OK!"
            db = MySQLdb.connect(
                host=DB_HOST,
                port=DB_PORT,
                user=DB_USERNAME,
                passwd=DB_PASSWORD,
                db=DB_PASSWORD)
            cur = db.cursor()
            code = ''.join(random.choice('ABCDEF0123456789') for i in range(10))
            print " --> CODE "+code
            cur.execute("INSERT INTO %s(username, code, created_time) "
                        "VALUES (%s, %s, NOW()) "
                        "ON DUPLICATE KEY UPDATE "
                        "code=%s,created_time=NOW()",
                        (DB_TABLE, username, code, code))
            cur.close()
            db.commit()
            db.close()
            return "Your code is %s it will last for 15 minutes." % code
        else:
            print " --> Failed login"
            return u"§4Couldn't authenticate you, please try again later"


if __name__ == "__main__":
    server = TeamspeakAuthServer(MC_MOTD, FAVICON)
    server.listen(MC_HOST, MC_PORT)
    server.run()