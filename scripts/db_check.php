<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=technova_store;charset=utf8mb4', 'root', '');
    $stmt = $pdo->query('SHOW TABLES');
    $tables = $stmt->fetchAll(PDO::FETCH_NUM);
    if (!$tables) {
        echo "No tables found or cannot access database.\n";
        exit(0);
    }
    foreach ($tables as $t) {
        echo $t[0] . PHP_EOL;
    }
} catch (PDOException $e) {
    echo 'PDOException: ' . $e->getMessage() . PHP_EOL;
}
