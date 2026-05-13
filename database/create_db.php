<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1', 'root', '');
    $pdo->exec('CREATE DATABASE IF NOT EXISTS kinerja_penelitian_dosen');
    echo "Database created or already exists\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
