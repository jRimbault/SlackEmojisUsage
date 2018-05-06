<?php

namespace Api\Database;

use Conserto\Path;
use PDO;

/**
 * Class Database
 * Singleton to PDO
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
}
