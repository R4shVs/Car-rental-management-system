<?php

class DB
{
    private $host;
    private $db_name;
    private $user;
    private $password;
    private $pdo;

    public function __construct()
    {
        include('config.ini.php');

        $this->host = $host;
        $this->db_name = $db_name;
        $this->user = $db_user;
        $this->password = $db_password;

        $this->connect();
    }

    protected function connect()
    {
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->db_name;
        $options = [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];

        try {
            $this->pdo = new PDO($dsn, $this->user, $this->password, $options);
        } catch (PDOException) {
            exit('Error 500');
        }
    }

    public function disconnect()
    {
        $this->pdo = null;
    }

    public function query($query)
    {
        try {
            return $this->pdo->query($query);
        } catch (PDOException) {
            if ($this->pdo->inTransaction())
                $this->rollBack();
            exit('Error 500');
        }
    }

    public function bindQuery($query, $params)
    {
        $stmt = $this->pdo->prepare($query);
        try {
            foreach ($params as $param => &$var) {
                $stmt->bindParam(":" . $param, $var);
            }

            $stmt->execute();
        } catch (PDOException $e) {
            if ($this->pdo->inTransaction())
                $this->rollBack();
            exit($e.'Error 500');
        }

        return $stmt;
    }

    public function exist($stmt)
    {
        if ($stmt->rowCount() > 0)
            return true;
        return false;
    }

    public function get($stmt)
    {
        return $stmt->rowCount() === 0 ? NULL : $stmt->fetch();
    }

    public function getAll($stmt)
    {
        return $stmt->rowCount() === 0 ? NULL : $stmt->fetchAll();
    }

    public function begin()
    {
        return $this->pdo->beginTransaction();
    }

    public function commit()
    {
        return $this->pdo->commit();
    }

    public function rollBack()
    {
        $this->pdo->rollBack();
        return $this->disconnect();
    }

    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }
}
