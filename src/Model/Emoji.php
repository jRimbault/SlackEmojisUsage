<?php

namespace Api\Model;

use Api\Database\Database;

/**
 * Models the relations between the Emoji object
 * and the database tables: 'emoji' and 'count'
 */
class Emoji implements \JsonSerializable
{
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
     * Returns the emoji named $name
     *
     * @param string $name
     *
     * @return Emoji|null
     */
    public static function find(string $name)
    {
        $result = self::emojiFetch(
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
     * Fetch a query from the php file containing all of the stored
     * SQL queries
     *
     * @param string $name of the query
     *
     * @return string SQL
     */
    private static function query(string $name): string
    {
        return (include 'queries.php')[$name];
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
    private static function emojiFetch(
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
     * Converts data from the BDD to a list of Emojis
     *
     * @param array[] data returned from the DB
     *
     * @return Emoji[]
     */
    private static function dataToEmojis($array): array
    {
        return array_map(
            self::class . '::toEmoji',
            $array
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
            self::sortEmojis(self::getAll()),
            0,
            $n
        );
    }

    /**
     * Sort desc
     *
     * @param Emoji[] $array
     *
     * @return Emoji[]
     */
    private static function sortEmojis($array): array
    {
        // go to hell usort
        usort($array, function (Emoji $eA, Emoji $eB) {
            return array_sum($eB->getCount()) <=> array_sum($eA->getCount());
        });
        return $array;
    }

    /**
     * Iterator over all emojis in the database.
     * The sequence of counts is guaranteed to be chronological.
     *
     * @yield Emoji
     */
    public static function getEach()
    {
        $dbh = Database::instance();
        // this statement will be re-used a number of times
        $statement = $dbh->prepare(self::query('emoji'));
        foreach (self::getAllEmojisNames() as $name) {
            yield self::toEmoji(
                self::emojiFetch($statement, $name, 168)
            );
        }
    }

    /**
     * Returns all the emojis.
     * The sequence of counts is in the chronological order.
     *
     * @return Emoji[]
     */
    public static function getAll()
    {
        return iterator_to_array(self::getEach());
    }

    /**
     * Get the names of all the custom emojis
     *
     * @return string[]
     */
    public static function getAllEmojisNames(): array
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

    public function getCount()
    {
        return $this->count;
    }

    public function getDates()
    {
        return $this->dates;
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
