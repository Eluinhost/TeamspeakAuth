# -*- coding: utf-8 -*-

from authserver import AuthServer

HOST = "localhost"
PORT = 35879
MOTD = u"§epublicuhc.com Auth Server"
FAVICON = None # or a path to a 64x64 .png

class ExampleAuthServer(AuthServer):
    def handle_auth(self, client_addr, server_addr, username, authed):
        print "%s/%s logged in" % (username, client_addr)

        if authed:
            print " --> OK!"

            #TODO BLOCKER generate a random code + insert into DB for the MC name

            return u"§lThanks! §rPlease check your web browser."

        else:
            print " --> FAILED!"
            return u"§4Couldn't authenticate you!"


if __name__ == "__main__":
    server = ExampleAuthServer(MOTD, FAVICON)
    server.listen(HOST, PORT)
    server.run()