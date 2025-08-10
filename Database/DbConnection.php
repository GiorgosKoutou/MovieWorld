<?php

namespace Database;

use PDO;
use PDOException;

class DbConnection
{

    private static $host = "localhost:3306";
    private static $db = "movieworld_db";
    private static $user = "root";
    private static $pass = "";
    private static $charset = "utf8mb4";

    private static $options = [

        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false

    ];

    private function __construct() {}

    //region Connection Function

    /**
     * Establishes and returns a PDO database connection using the configured settings.
     *
     * @return PDO|null Returns a PDO instance on success, or null on failure.
     */
    public static function connect()
    {

        // Build the DSN (Data Source Name) string for the PDO connection
        $dsn = "mysql:host=" . self::$host . ";dbname=" . self::$db . ";charset=" . self::$charset;

        try {

            // Attempt to create and return a new PDO instance with the provided credentials and options
            return new PDO($dsn, self::$user, self::$pass, self::$options);

        } catch (PDOException $e) {

            // Output an error message if the connection fails
            $_SESSION['connectionError'] = "Connection Fail";
        }

        // Return null if the connection could not be established
        return null;
    }

    //endregion
}


