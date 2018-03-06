<?php

namespace Api\Controller;

use Conserto\Json;
use Conserto\Path;
use Conserto\Controller;
use Conserto\Server\Http\Request;


class Slack extends Controller
{
    private $statsFile = '/Slats/stats.json';

    public function emojis(Request $request)
    {
        Json::writeToFile($_POST, new Path('/var/cache/postdump.json'));

        return $this->getStats();
    }

    public function getStats()
    {
        $tenMinutes = 600;
        $path = (new Path($this->statsFile))->get();
        $fmtime = filemtime($path);
        // if (!$fmtime || (time() - $fmtime > $tenMinutes)) {
        //     $this->makeStats();
        //     return "You have to wait a bit.";
        // }
        return $this->makeStats();
    }

    private function getNfirst($n = 10)
    {
        $data = Json::DecodeFile(new Path($this->statsFile));
        $emojis = $data['emoji'];
        arsort($emojis);
        return array_slice($emojis, 0, $n, true);
    }

    private function makeStats()
    {
        $mostUsed = $this->getNfirst();
        $text = [];
        foreach ($mostUsed as $name => $value) {
            $text[] = ":$name: $value";
        }
        return join(PHP_EOL, $text);
    }
}
