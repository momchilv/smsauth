<?php

class Database
{
    protected $connection;

    public function __construct()
    {
        try {
            $config = parse_ini_file("config.ini");
            $this->connection = new PDO("mysql:host=" . $config['host'] . ";dbname=" . $config['database'], $config['user'], $config["password"]);

            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $this->connection->query('USE ' . $config['database'] . ';');
        } catch (PDOException $err) {
            echo "ERROR: Unable to connect: " . $err->getMessage();
        };
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function query($query)
    {
        return $this->connection->query($query);
    }

    public function fetchRow($query, $fields)
    {
        $query = $this->connection->prepare($query);
        $query->execute($fields);
        return $query->fetch();
    }

    public function fetchAll($query, $fields)
    {
        $query = $this->connection->prepare($query);
        $query->execute($fields);
        return $query->fetchAll();
    }

    public function insertRow($query, $fields)
    {
        $this->fetchAll($query, $fields);
        return $this->lastInsertId();
    }

    public function lastInsertId()
    {
        return $this->connection->lastInsertId();
    }

    public function updateQuery($query, $fields)
    {
        $query = $this->connection->prepare($query);
        $query->execute($fields);
        $query->fetchAll();
        return $query->rowCount();
    }

    public function closeConnection()
    {
        $this->connection = NULL;
    }
}
