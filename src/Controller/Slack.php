<?php

namespace Api\Controller;

use Conserto\Json;
use Conserto\Path;
use Api\Model\Emoji;
use Conserto\Controller;
use Conserto\Utils\Config;
use Conserto\Http\Request;


class Slack extends Controller
{
    const snapshot = '/Slats/stats.json';

    /**
     * Called by Slack and sends back a message containing the top 10
     * current emojis
     *
     * @param Request $request
     * @return string
     *
     * @Route("/slack/bot/top/emoji", methods="POST")
     * @todo add parameter to route
     */
    public function emojisSlackMessage(Request $request, $n = 10): string
    {
        return $this->buildSlackMessage((int)$n);
    }

    /**
     * Builds a string of the top $n emojis EOL separated,
     * to be used as a message in Slack
     *
     * @param int $n number of emojis
     * @return string
     */
    private function buildSlackMessage(int $n = 10): string
    {
        return randomSentence() . PHP_EOL . join(
            PHP_EOL,
            array_reduce(
                array_slice(
                    $this->getLastSnapshot(),
                    0,
                    $n
                ),
                function ($text, $emoji) {
                    array_push($text, ":$emoji[1]: $emoji[0]");
                    return $text;
                },
                []
            )
        );
    }

    /**
     * @Route("/slack/statistics/emoji", methods="GET")
     *
     * Displays the list of emojis to a normal web user
     *
     * @param Request $request
     * @return string
     */
    public function emojisHtml(Request $request): string
    {
        return $this->render('slack/statistics/emoji.html.twig', [
            'emojis' => $this->getLastSnapshot(),
            'date' => filemtime(new Path(self::snapshot)),
        ]);
    }

    /**
     * @Route("/slack/list/emoji", methods="POST")
     *
     * Returns the JSON list of emojis
     *
     * @return string
     */
    public function emojisList(): string
    {
        header('Content-Type: application/json');
        return file_get_contents(new Path(self::snapshot));
    }

    /**
     * @Route("/slack/data/emoji/{n}", methods="POST")
     *
     * Returns the JSON data about the $n top emojis or about
     * the emoji named $n
     *
     * @param Request $request
     * @param mixed $n number of emojis or name of an emoji
     * @return string
     */
    public function emojisData(Request $request, $n = 5): string
    {
        if (is_numeric($n)) {
            return $this->json(Emoji::sortedGetAll((int)$n));
        }
        if (in_array((string)$n, Emoji::getAllEmojisNames())) {
            return $this->json([Emoji::find((string)$n)]);
        }
        return $this->json([], 400);
    }

    private function getLastSnapshot(): array
    {
        return Json::decodeFile(new Path(self::snapshot));
    }
}

/**
 * Returns a random sentence
 *
 * @return string sentence
 */
function randomSentence(): string
{
    $s = include 'sentences.php';
    return $s[random_int(0, count($s) - 1)];
}
