<?php

namespace Api\Model;

use Conserto\Path;
use Api\Database\Database;


class Emoji
{
    private $name = '';
    private $url = '';
    private $count = [];
    private static $queries = '/src/Model/queries.php';

    private function __construct($name, $url, $count)
    {
        $this->name = $name;
        $this->url = $url;
        $this->count = $count;
    }

    public static function InitAll()
    {
        $db = Database::Instance();
        $emojis = [];
        $statement = $db->prepare(self::getSingleEmoji());
        foreach (self::getAllEmojisNames() as $name) {
            $result = $db->executeFetchAll($statement, [$name, $name])[0];
            $emojis[] = new Emoji(
                $result['name'],
                $result['url'],
                array_map('intval', explode(',', $result['count']))
            );
        }
        return $emojis;
    }

    private static function getSingleEmoji(): string
    {
        $queries = include new Path(self::$queries);
        return $queries['emoji'];
    }

    private static function getAllEmojisNames(): array
    {
        $queries = include new Path(self::$queries);
        return array_map(
            function ($value) {
                return $value['name'];
            },
            Database::Instance()->simpleQuery($queries['names'])
        );
    }

    public function getName() { return $this->name; }
    public function getUrl() { return $this->url; }
    public function getCount() { return $this->count; }
}
