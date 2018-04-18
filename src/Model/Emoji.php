<?php

namespace Api\Model;

use Conserto\Path;
use Api\Database\Database;


class Emoji implements \JsonSerializable
{
    private $name = '';
    private $url = '';
    private $count = [];
    private static $queries = 'queries.php';

    public function __construct($name = '', $url = '', $count = [])
    {
        $this->name = $name;
        $this->url = $url;
        $this->count = $count;
    }

    public static function find($name)
    {
        $dbh = Database::Instance();
        $query = (include self::$queries)['emoji'];
        $statement = $dbh->prepare($query);
        $result = $dbh->executeFetchAll($statement, [$name, $name])[0];
        if (!$result) {
            return null;
        }
        return self::toEmoji($result);
    }

    public static function getAll()
    {
        $dbh = Database::Instance();
        $query = (include self::$queries)['emoji'];
        $statement = $dbh->prepare($query);
        foreach (self::getAllEmojisNames() as $name) {
            $result = $dbh->executeFetchAll($statement, [$name, $name])[0];
            yield self::toEmoji($result);
        }
    }

    private static function getAllEmojisNames(): array
    {
        return array_map(
            function ($value) {
                return $value['name'];
            },
            Database::Instance()->simpleQuery(
                (include self::$queries)['names']
            )
        );
    }

    /**
     * Returns all the data about all the emojis in one request.
     * BUT, there's no guarantee the sequence of counts is in the
     * chronological order.
     *
     * @return Emoji[]
     */
    public static function getAllEmojisDataOneShot(): array
    {
        return self::dataToEmojis(
            Database::Instance()->simpleQuery(
                (include 'queries.php')['general']
            )
        );
    }

    /**
     * Returns an array of Emoji
     *
     * @return Emoji[]
     */
    private static function dataToEmojis($array): array
    {
        return array_map(
            function ($emoji) {
                return self::toEmoji($emoji);
            },
            $array
        );
    }

    /**
     * Makes an instance of Emoji from an array
     *
     * @param array $array should have the indexes: name, url, count
     *
     * @return Emoji
     */
    private static function toEmoji($array): Emoji
    {
        return new Emoji(
            $array['name'] ?? null,
            $array['url'] ?? null,
            array_map('intval', explode(',', $array['count'] ?? []))
        );
    }

    public function getName() { return $this->name; }
    public function getUrl() { return $this->url; }
    public function getCount() { return $this->count; }

    public function jsonSerialize()
    {
        return [
            $this->name,
            $this->url,
            $this->count,
        ];
    }

    public function __toString()
    {
        return json_encode(
            $this->jsonSerialize(),
            JSON_UNESCAPED_UNICODE |
            JSON_UNESCAPED_SLASHES
        );
    }
}
