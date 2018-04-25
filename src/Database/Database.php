<?php

namespace Api\Database;

use Conserto\Path;


class Database extends \PDO
{
    private static $instance;
    private static $dbfile = '/var/resources/api.db';

    public function __construct()
    {
        parent::__construct('sqlite:' . new Path(self::$dbfile));
    }

    public static function instance(): self
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function executeFetchAll($statement, $params = []): array
    {
        if ($statement && $statement->execute($params)) {
            return $statement->fetchAll(\PDO::FETCH_ASSOC);
        }
        return [];
    }

    public function simpleQuery($query): array
    {
        return $this->executeFetchAll($this->prepare($query));
    }
}
