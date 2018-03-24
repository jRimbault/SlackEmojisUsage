<?php

namespace Api\Controller;

use Conserto\Json;
use Conserto\Path;
use Conserto\Controller;
use Conserto\Utils\Config;
use Conserto\Server\Http\Request;


class Slack extends Controller
{
    /**
     * @var string path to the json file containing the last counts
     */
    private $statsFile = '/Slats/stats.json';

    /**
     * @Route("/slack/statistics/emoji", methods="POST")
     *
     * Called by Slack and sends back a message containing the top 10
     * current emojis
     *
     * @param Request $request
     * @return string
     */
    public function emojisSlackMessage(Request $request)
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

    /**
     * @Route("/slack/statistics/emoji", methods="GET")
     *
     * Displays the list of emojis to a normal web user
     *
     * @param Request $request
     * @return string
     */
    public function emojisHtml(Request $request)
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

    /**
     * @Route("/slack/list/emoji", methods="POST")
     *
     * Returns the JSON list of emojis
     *
     * @return string
     */
    public function emojisList()
    {
        header('Content-Type: application/json');
        return file_get_contents(new Path('/Slats/stats.json'));
    }

    /**
     * Builds a string of the top $n emojis EOL separated,
     * to be used as a message in Slack
     *
     * @param int $n number of emojis
     * @return string
     */
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
