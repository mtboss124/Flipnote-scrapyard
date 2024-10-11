from twisted.web import resource
from twisted.internet import reactor
import time
from hatena import Log, LoadHatenadirStructure, Setup, ServerLog, Silent, NotFound, ds, Root, UgoRoot, FileResource, UGOXMLResource, FolderResource

from hatena import ServerLog, Silent
from DB import Database
from Hatenatools import TMB

class PyResource(resource.Resource):
    isLeaf = True

    def render_GET(self, request):
        ServerLog.write("%s got 403 when requesting post/flipnote.post with GET" % (request.getClientIP()), Silent)
        request.setResponseCode(405)
        return "405 - Method Not Allowed"

    def render_POST(self, request):
        data = request.content.read()
        channel = ""
        creator_id = ""
        flipnote_id = ""
        if "channel" in request.args:
            channel = request.args["channel"][0]
        if "creator_id" in request.args:
            creator_id = request.args["creator_id"][0]
        if "flipnote_id" in request.args:
            flipnote_id = request.args["flipnote_id"][0]
        print(creator_id + flipnote_id)
        add = Database.AddComment(data, creator_id, flipnote_id, channel)
        if add:
            ServerLog.write("%s successfully uploaded \"%s.ppm\"" % (request.getClientIP(), add[1]), Silent)
            request.setResponseCode(200)
        else:
            ServerLog.write("%s tried to upload a flipnote, but failed..." % request.getClientIP(), Silent)
            request.setResponseCode(500)
        return ""