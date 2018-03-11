<?php

namespace Api\Controller;

use Conserto\Json;
use Conserto\Path;
use Conserto\Controller;
use Conserto\Utils\Config;
use Conserto\Server\Http\Request;


class Slack extends Controller
{
    private $statsFile = '/Slats/stats.json';

    /** post route */
    public function emojis(Request $request)
    {
        $config = Config::Instance()->getArray();
        $return = '401';

        if ($request->post()->get('token') === $config['verificationtoken']) {
            Json::writeToFile($_POST, new Path('/var/cache/postdump.json'));
            $request->post()->do('https://slack.com/api/chat.postMessage', [
                'token' => $config['token'],
                'channel' => $request->post()->get('channel_id'),
                'text' => $this->slackMessage()
            ]);
            $return = '';
        }

        return $return;
    }

    public function emojishtml(Request $request)
    {
        $data = Json::DecodeFile(new Path($this->statsFile));
        $data = $this->sortStructure($data);

        return $this->render('slack/statistics/emoji.html.twig', [
            'emojis' => $data,
            'date' => filemtime(new Path($this->statsFile)),
        ]);
    }

    public function emojislist(Request $request)
    {
        $data = Json::DecodeFile(new Path($this->statsFile));
        // $data = $this->sortStructure($data);

        return $this->render('slack/list/emoji.html.twig', [
            'emojis' => array_reverse($data),
            'date' => filemtime(new Path($this->statsFile)),
        ]);
    }

    private function getNfirst($n = 10)
    {
        $data = Json::DecodeFile(new Path($this->statsFile));
        $data = $this->sortStructure($data);

        return array_slice($data, 0, $n);
    }

    /** contruct a slack message */
    private function slackMessage()
    {
        $mostUsed = $this->getNfirst();
        $text = [];
        foreach ($mostUsed as $emoji) {
            $text[] = ":$emoji[0]: $emoji[1]";
        }
        $text[] = 'https://' .  $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        return join(PHP_EOL, $text);
    }

    private function sortStructure(array $data)
    {
        return array_reverse(array_filter($data, function($value) {
            return (bool) $value[0];
        }));
    }
}
