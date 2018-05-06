<?php

namespace Api\Database;

use Conserto\Path;
use PDO;

/**
 * Class Database
 *
 * Some shortcut methods to PDO
 * And singleton method
 *
 * @package Api\Database
 */
class Database extends PDO
{
    const driver = 'sqlite';
    const dbFile = '/var/resources/api.db';
    private static $instance;

    public function __construct()
    {
        $path = new Path(self::dbFile);
        parent::__construct(self::driver . ':' . $path->get());
    }

    /**
     * Get back a singleton of the class Database
     * @return Database
     */
    public static function instance(): self
    {
        return self::$instance ?? self::$instance = new self();
    }

    /**
     * For queries without dynamic parameters
     * @param string $query
     * @return array
     */
    public function simpleQuery(string $query): array
    {
        return $this->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }
}
