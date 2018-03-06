<?php

namespace Api\Controller;

use Conserto\Json;
use Conserto\Path;
use Conserto\Controller;
use Conserto\Server\Http\Request;


class Slack extends Controller
{
    public function emojis(Request $request)
    {
        Json::writeToFile($_POST, new Path('/var/cache/postdump.json'));

        return Json::Response([
            'status' => 200,
            'message' => 'Not implemented',
        ], 200);
    }
}
