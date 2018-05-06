<?php

namespace Api;

use Api\Controller\Slack;
use Conserto\Routing\Router;


class Kernel
{
    private $router;

    public function __construct()
    {
        $this->router = new Router();
        $this->slackRoutes();
    }

    public function start()
    {
        return $this->router->start();
    }

    public function slackRoutes()
    {
        $this->router
            ->get('/slack/statistics/emoji', Slack::class, 'emojisHtml')
            ->post('/slack/bot/top/emoji', Slack::class, 'emojisSlackMessage')
            ->post('/slack/data/emoji/{n}', Slack::class, 'emojisData');
    }
}
