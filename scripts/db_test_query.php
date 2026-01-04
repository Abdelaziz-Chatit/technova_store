<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=technova_store;charset=utf8mb4', 'root', '');
    $tables = ['category', 'doctrine_migration_versions'];
    foreach ($tables as $t) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as c FROM `$t`");
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "$t: " . ($row['c'] ?? 'NULL') . PHP_EOL;
        } catch (PDOException $e) {
            echo "$t query error: " . $e->getMessage() . PHP_EOL;
        }
    }
} catch (PDOException $e) {
    echo 'PDOException: ' . $e->getMessage() . PHP_EOL;
}
