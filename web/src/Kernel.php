<?php

namespace Api;

use Conserto\Path;
use Conserto\Controller;
use Conserto\Utils\Config;
use Conserto\Server\Router;
use Api\Controller\Slack;


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
        $this->router->post('/slack/statistics/emoji', Slack::class, 'emojisSlackMessage');
        $this->router->get('/slack/statistics/emoji', Slack::class, 'emojisHtml');
        $this->router->post('/slack/list/emoji', Slack::class, 'emojisList');
    }
}