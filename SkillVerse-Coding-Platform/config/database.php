<?php
require_once __DIR__ . '/config.php';

function getDBConnection() {
    static $pdo = null;
    
    if ($pdo !== null) {
        return $pdo;
    }
    
    try {
        // First try connecting directly to the database
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        // If database does not exist, let's try to connect to server and create it
        try {
            $dsn = "mysql:host=" . DB_HOST . ";charset=utf8mb4";
            $temp_pdo = new PDO($dsn, DB_USER, DB_PASS);
            $temp_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Create database
            $temp_pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            
            // Reconnect to the newly created database
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
            
            // Run schema if it exists
            $schemaFile = dirname(__DIR__) . '/database/schema.sql';
            if (file_exists($schemaFile)) {
                $sql = file_get_contents($schemaFile);
                // Remove USE statement if it conflicts or let it run
                $pdo->exec($sql);
            }
            return $pdo;
        } catch (PDOException $ex) {
            die("Database connection failed: " . $ex->getMessage());
        }
    }
}
?>
