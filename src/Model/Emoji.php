<?php

namespace Api\Model;

use Conserto\Path;
use Api\Database\Database;


class Emoji implements \JsonSerializable
{
    private $name = '';
    private $url = '';
    private $count = [];
    private $dates = [];
    private static $queries = 'queries.php';

    public function __construct($name = '', $url = '', $count = [], $dates = [])
    {
        $this->name = $name;
        $this->url = $url;
        $this->count = $count;
        $this->dates = $dates;
    }

    /**
     * Returns the emoji named $name
     *
     * @param string $name
     *
     * @return Emoji|null
     */
    public static function find(string $name)
    {
        $dbh = Database::instance();
        $query = (include self::$queries)['emoji'];
        $statement = $dbh->prepare($query);
        $result = $dbh->executeFetchAll($statement, [$name, $name])[0];
        if (!$result) {
            return null;
        }
        return self::toEmoji($result);
    }

    /**
     * Iterator over all emojis in the database.
     * The sequence of counts is guaranteed to be chronological.
     *
     * @yield Emoji
     */
    public static function getAll()
    {
        $dbh = Database::instance();
        // this statement will be re-used a number of times
        $statement = $dbh->prepare((include self::$queries)['emoji']);
        foreach (self::getAllEmojisNames() as $name) {
            yield self::toEmoji(
                $dbh->executeFetchAll($statement, [$name, $name])[0]
            );
        }
    }

    public static function getAllEmojisNames(): array
    {
        return array_map(
            function ($value) {
                return $value['name'];
            },
            Database::instance()->simpleQuery(
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
            Database::instance()->simpleQuery(
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
        return array_map(self::class . '::toEmoji', $array);
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
            array_map('intval', explode(',', $array['count'] ?? [])),
            explode(',', $array['date'] ?? '')
        );
    }

    public function getName()
    {
        return $this->name;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getCount()
    {
        return $this->count;
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'url' => $this->url,
            'data' => [
                $this->count,
                $this->dates,
            ],
        ];
    }

    public function __toString(): string
    {
        return json_encode(
            $this->jsonSerialize(),
            JSON_UNESCAPED_UNICODE |
            JSON_UNESCAPED_SLASHES
        );
    }

    /**
     * Get the sorted $n top emojis
     *
     * @param int $n
     * @return Emoji[]
     */
    public static function sortedGetAll($n = null): array
    {
        return array_slice(
            self::sortEmojis(iterator_to_array(self::getAll())),
            0,
            $n
        );
    }

    /** sort desc */
    private function sortEmojis($array): array
    {
        // go to hell usort
        usort($array, function ($eA, $eB) {
            $sumA = array_sum($eA->getCount());
            $sumB = array_sum($eB->getCount());
            if ($sumA === $sumB) {
                return 0;
            }
            return $sumA < $sumB ? 1 : -1;
        });
        return $array;
    }
}
