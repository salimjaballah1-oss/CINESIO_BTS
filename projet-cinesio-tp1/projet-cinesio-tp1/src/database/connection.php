<?php
require_once __DIR__ . '/../config/database.php';

function connectDatabase(): PDO
{
    try {
        $dsn = "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME;
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ];

        $connexion = new PDO($dsn, DB_USER, DB_PASSWORD, $options);
        return $connexion;
    } catch (PDOException $erreur) {
        die("Erreur de connexion : " . $erreur->getMessage());
    }
}
?>
