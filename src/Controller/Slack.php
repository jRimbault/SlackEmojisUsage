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
        return $this->render('slack/statistics/emoji.html.twig', [
            'emojis' => $this->sortStructure(
                Json::DecodeFile(new Path($this->statsFile))
            ),
            'date' => filemtime(new Path($this->statsFile)),
        ]);
    }

    public function emojislist(Request $request)
    {
        return $this->render('slack/list/emoji.html.twig', [
            'emojis' => array_reverse(
                Json::DecodeFile(new Path($this->statsFile))
            ),
            'date' => filemtime(new Path($this->statsFile)),
        ]);
    }

    private function getNfirst(int $n = 10)
    {
        return array_slice(
            $this->sortStructure(Json::DecodeFile(new Path($this->statsFile))),
            0, $n
        );
    }

    /** contruct a slack message */
    private function slackMessage()
    {
        return join(PHP_EOL, array_reduce(
            $this->getNfirst(),
            function($text, $emoji) {
                array_push($text, ":$emoji[1]: $emoji[0]");
                return $text;
            },
            []
        ));
    }

    private function sortStructure(array $data)
    {
        return array_reverse(array_filter($data, function($value) {
            return (bool) $value[0];
        }));
    }
}
