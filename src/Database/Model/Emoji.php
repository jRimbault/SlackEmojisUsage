<?php

namespace Api\Database\Model;

use Api\Database\Database;
use Conserto\Json;
use Conserto\Path;

/**
 * Models the relations between the Emoji object
 * and the database tables: 'emoji' and 'count'
 */
class Emoji implements \JsonSerializable
{
    const queries = 'queries.php';
    const snapshot = '/Slats/stats.json';
    private $name = '';
    private $url = '';
    private $count = [];
    private $dates = [];

    public function __construct($name = '', $url = '', $count = [], $dates = [])
    {
        $this->name = $name;
        $this->url = $url;
        $this->count = $count;
        $this->dates = $dates;
    }

    /**
     * Returns the last count of every emoji
     *
     * @return array
     */
    public static function snapshot(): array
    {
        return Json::decodeFile(new Path(self::snapshot));
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
        $result = self::fetch(
            Database::instance()->prepare(self::query('emoji')),
            $name,
            168
        );
        if (!$result) {
            return null;
        }
        return self::toEmoji($result);
    }

    /**
     * The particular semantics of the query makes it painful
     * to write in several place
     *
     * @param \PDOStatement $statement
     * @param string $name
     * @param int $limit
     *
     * @return array
     */
    private static function fetch(
        \PDOStatement $statement,
        string $name,
        int $limit = 168
    ) {
        return Database::instance()->executeFetchAll(
            $statement,
            [
                'name' => $name,
                'limit' => $limit
            ]
        )[0];
    }

    /**
     * Fetch a query from the php file containing all of the stored
     * SQL queries
     *
     * @param string $name of the query
     *
     * @return string SQL
     */
    private static function query(string $name): string
    {
        return (include self::queries)[$name];
    }

    /**
     * Returns all the emojis sorted.
     * Or get the sorted $n top emojis
     *
     * The sequence of counts is in the chronological order.
     *
     * @param int $n
     * @return Emoji[]
     */
    public static function all($n = null): array
    {
        return array_slice(
            self::sort(iterator_to_array(self::each())),
            0,
            $n
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
            array_map('intval', explode(',', $array['count'] ?? '')),
            explode(',', $array['date'] ?? '')
        );
    }

    /**
     * Sort desc
     *
     * @param Emoji[] $array
     *
     * @return Emoji[]
     */
    private static function sort($array): array
    {
        // go to hell usort
        usort($array, function (Emoji $a, Emoji $b) {
            return array_sum($b->getCount()) <=> array_sum($a->getCount());
        });
        return $array;
    }

    /**
     * Iterator over all emojis in the database.
     * The sequence of counts is guaranteed to be chronological.
     *
     * @yield Emoji
     */
    public static function each()
    {
        $dbh = Database::instance();
        // this statement will be re-used a number of times
        $statement = $dbh->prepare(self::query('emoji'));
        foreach (self::names() as $name) {
            yield self::toEmoji(
                self::fetch($statement, $name, 168)
            );
        }
    }

    /**
     * Get the names of all the custom emojis
     *
     * @return string[]
     */
    public static function names(): array
    {
        return array_map(
            function ($value) {
                return $value['name'];
            },
            Database::instance()->simpleQuery(
                self::query('names')
            )
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

    public function getDates()
    {
        return $this->dates;
    }

    public function getCount()
    {
        return $this->count;
    }

    public function __toString(): string
    {
        return json_encode(
            $this->jsonSerialize(),
            JSON_UNESCAPED_UNICODE |
            JSON_UNESCAPED_SLASHES
        );
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
}