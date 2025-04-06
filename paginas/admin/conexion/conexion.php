<?php
function connectDB() {
    // Database configuration
    $host = 'localhost';
    $db = 'jatshirts';
    $user = 'root';
    $pass = '';
    
    // Set connection options
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        // Create PDO instance
        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, $options);
        return $pdo;
    } catch (PDOException $e) {
        // Log the error instead of displaying it directly
        error_log("Database connection error [" . date('Y-m-d H:i:s') . "]: " . $e->getMessage() . "\nHost: $host\nDB: $db\nUser: $user");
        die("Error establishing database connection. Please verify the database server is running and credentials are correct.");
    }
}
?>