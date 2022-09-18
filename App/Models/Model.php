<?php

namespace App\Models;

use App\Libraries\Dotenv;
use Exception;
use PDO;
use PDOException;
use stdClass;

class Model
{
    private $connection;
    private $connectionError = null;
    protected $lastQuery = '';
    protected $databaseDebug = false;
    public function __construct()
    {
        $this->connect();
    }

    protected function connect()
    {
        $this->connectionError = null;
        $this->disconnect();
        try {
            $dotenv = new Dotenv();
            $databaseUser = $dotenv->get('databaseUser');
            $databaseHost = $dotenv->get('databaseHost');
            $databasePass = $dotenv->get('databasePass');
            $databaseName = $dotenv->get('databaseName');
            $databasePort = $dotenv->get('databasePort');
            $this->databaseDebug = in_array($dotenv->get('databaseDebug'), ['true', 1]);
            $databasePort = !empty($databasePort) ? $databasePort : 3306;
            $this->connection = new PDO("mysql:host=$databaseHost;port=$databasePort;dbname=$databaseName", $databaseUser, $databasePass);
        } catch (PDOException $e) {
            return $this->handleError($e);
        }
    }

    protected function disconnect()
    {
        if ($this->connection) {
            $this->connection = null;
        }
    }
    public function __destruct()
    {
        $this->disconnect();
    }

    protected function query($sqlQuery)
    {
        try {
            $query = $this->connection->query($sqlQuery);
            $this->lastQuery = $query->queryString;
            return $this->handleSuccess($query);
        } catch (PDOException $e) {
            return $this->handleError($e);
        }
    }

    /**
     * Get the value of connectionError
     */
    public function getConnectionError()
    {
        return $this->connectionError;
    }

    private function handleError($e)
    {
        if ($this->databaseDebug === true) {
            pre($e, 1);
        }
        $this->connectionError = $e;
        return false;
    }
    private function handleSuccess($result)
    {
        $return = new stdClass();
        try {
            $data = $result->fetchAll(PDO::FETCH_CLASS);
            $return->rowCount = count($data);
            if ($return->rowCount)
                $return->data = $data;
            return $return;
        } catch (Exception $e) {
            return false;
        }
    }
}
