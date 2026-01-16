<?php

namespace App\Rdb;

use mysqli;
use RuntimeException;

class SqlHelper {
    public function __construct() {
        $this->pingDb();
    }

    public function pingDb(): void {
        $connection = $this->openDbConnection();
        $connection->close();
    }

    public function openDbConnection(): mysqli {
        $host = $_ENV["DB_HOST"] ?? 'db';
        $port = $_ENV["DB_PORT"] ?? 3306;
        $user = $_ENV["DB_USERNAME"] ?? 'root';
        $password = $_ENV["DB_PASSWORD"] ?? 'root';
        $database = $_ENV["DB_NAME"] ?? 'countries_db';

        $connection = new mysqli(
            hostname: $host,
            port: $port,
            username: $user,
            password: $password,
            database: $database
        );

        if ($connection->connect_errno) {
            throw new RuntimeException("Failed to connect to MySQL: " . $connection->connect_error);
        }

        return $connection;
    }
}