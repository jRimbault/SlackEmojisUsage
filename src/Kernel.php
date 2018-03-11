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

    private function setConfiguration()
    {
        Config::setConfigFile(new Path('/config/app.json'));
        Controller::setCache(new Path('/var/cache'));
        Controller::setTemplate(new Path('/config/views'));
    }

    public function __construct()
    {
        $this->setConfiguration();
        $this->router = new Router();
        $this->slackRoutes();
    }

    public function start()
    {
        return $this->router->start();
    }

    public function slackRoutes()
    {
        $this->router->post('/slack/statistics/emoji', Slack::class, 'emojis');
        $this->router->get('/slack/statistics/emoji', Slack::class, 'emojishtml');
        $this->router->get('/slack/list/emoji', Slack::class, 'emojislist');
    }
}
