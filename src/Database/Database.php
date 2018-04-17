<?php

namespace Api\Database;

use Conserto\Path;


class Database extends \PDO
{
    private static $instance;
    private static $dbfile = '/var/resources/emoji.db';

    private function __construct()
    {
        parent::__construct('sqlite:' . new Path(self::$dbfile));
    }

    public function Instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function executeFetchAll($statement, $params = [])
    {
        if ($statement && $statement->execute($params)) {
            return $statement->fetchAll(\PDO::FETCH_ASSOC);
        }
        return null;
    }

    public function simpleQuery($query)
    {
        return $this->executeFetchAll($this->prepare($query));
    }
}
