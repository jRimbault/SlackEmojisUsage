<?php

namespace Api\Controller;

use Api\Database\Database;
use Api\Database\Model\Emoji;
use Conserto\Controller;
use Conserto\Error\RuntimeError;
use Conserto\Http\Request;
use Conserto\Path;


class Slack extends Controller
{
    /**
     * Called by Slack and sends back a message containing the top 10
     * current emojis
     *
     * @param Request $request
     * @param mixed $n
     * @return string
     *
     * @Route("/slack/bot/top/emoji", methods="POST")
     * @todo add parameter to route in Kernel and in Slack settings
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
                        Emoji::snapshot(),
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
        try {
            return $this->render('slack/statistics/emoji.html.twig', [
                'emojis' => Emoji::snapshot(),
                'date' => filemtime(new Path(Database::dbFile)),
            ]);
        } catch (RuntimeError $e) {
            http_response_code(500);
            return 'Whoops';
        }
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
            return $this->json(Emoji::all((int)$n));
        }
        if (in_array((string)$n, Emoji::names())) {
            return $this->json([Emoji::find((string)$n)]);
        }
        return $this->json([], 400);
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
    return $s[array_rand($s, 1)];
}
