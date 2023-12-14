<?php

namespace Core;

use PDO;

class Database
{
    public $connection;
    public $query;

    public function __construct($config, string $db = 'mysql')
    {
        $dsn = $db . ':' . http_build_query($config, '', ';');
        $db_user = $db === 'mysql' ? $config['username'] : $config['user'];

        $this->connection = new PDO($dsn, $db_user, $config['password'], [
          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    }

    public function query(string $query, array $params = []): self
    {
        $this->query = $this->connection->prepare($query);
        $this->query->execute($params);

        return $this;
    }

    public function get()
    {
        return $this->query->fetchAll();
    }

    public function find()
    {
        return $this->query->fetch();
    }

    public function lastInsertId()
    {
        return $this->connection->lastInsertId();
    }

    public function rowCount()
    {
        return $this->query->rowCount();
    }

    public function findOrFail()
    {
        $result = $this->find();

        if(!$result) {
            abort();
        }

        return $result;
    }
}
