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
        $stats = Json::DecodeFile(new Path($this->statsFile));
        arsort($stats['emoji']);
        $stats['emoji'] = array_filter($stats['emoji']);
        $emojis = [];
        foreach ($stats['emoji'] as $name => $count) {
            $emojis[] = [
                'name' => $name,
                'count' => $count,
                'url' => $stats['urls'][$name],
            ];
        }

        return $this->render('slack/statistics/emoji.html.twig', [
            'emojis' => $emojis,
            'date' => date('H:i:s', filemtime(new Path($this->statsFile))),
        ]);
    }


    private function getNfirst($n = 10)
    {
        $emojis = Json::DecodeFile(new Path($this->statsFile))['emoji'];
        arsort($emojis);

        return array_slice($emojis, 0, $n, true);
    }

    /** contruct a slack message */
    private function slackMessage()
    {
        $mostUsed = $this->getNfirst();
        $text = [];
        foreach ($mostUsed as $name => $value) {
            $text[] = ":$name: $value";
        }

        return join(PHP_EOL, $text);
    }
}
