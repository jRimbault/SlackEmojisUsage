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

        if ($request->post()->get('token') !== $config['verificationtoken']) {
            http_response_code(401);
            return '401';
        }

        Json::writeToFile($_POST, new Path('/var/cache/postdump.json'));
        $request->post()->do('https://slack.com/api/chat.postMessage', [
            'token' => $config['token'],
            'channel' => $request->post()->get('channel_id'),
            'text' => $this->slackMessage()
        ]);

        return '';
    }

    public function emojishtml(Request $request)
    {
        $emojis = Json::DecodeFile(new Path($this->statsFile));
        return $this->render('slack/statistics/emoji.html.twig', [
            'emojis' => $emojis,
            'date' => filemtime(new Path($this->statsFile)),
            'total' => array_reduce($emojis, function($total, $emoji) {
                return $total += $emoji[0];
            }, 0)
        ]);
    }

    public function emojislist(Request $request)
    {
        return $this->render('slack/list/emoji.html.twig', [
            'emojis' => Json::DecodeFile(new Path($this->statsFile)),
            'date' => filemtime(new Path($this->statsFile)),
        ]);
    }

    public function emojisjson()
    {
        header('Content-Type: application/json');
        return file_get_contents(new Path('/Slats/stats.json'));
    }

    /** contruct a slack message */
    private function slackMessage(int $n = 10): string
    {
        return join(PHP_EOL, array_reduce(
            array_slice(
                Json::DecodeFile(new Path($this->statsFile)),
                0, $n
            ),
            function($text, $emoji) {
                array_push($text, ":$emoji[1]: $emoji[0]");
                return $text;
            },
            []
        ));
    }
}
