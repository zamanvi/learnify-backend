<?php
set_time_limit(300);
ini_set('memory_limit', '512M');

$host = getenv('DB_HOST');
$port = getenv('DB_PORT') ?: 3306;
$db   = getenv('DB_DATABASE');
$user = getenv('DB_USERNAME');
$pass = getenv('DB_PASSWORD');

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
    $pdo->exec("SET sql_mode=''");
    $pdo->exec("SET NAMES utf8mb4");

    $lines = file(__DIR__ . '/db_full.sql', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $buffer = '';
    $success = 0;
    $errors = [];

    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || str_starts_with($line, '--') || str_starts_with($line, '#') || str_starts_with($line, '/*')) continue;
        
        $buffer .= $line . "\n";
        
        if (str_ends_with($line, ';')) {
            $stmt = trim($buffer);
            if (!empty($stmt)) {
                try {
                    $pdo->exec($stmt);
                    $success++;
                } catch (Exception $e) {
                    $msg = $e->getMessage();
                    // Skip "already exists" errors
                    if (!str_contains($msg, 'already exists') && !str_contains($msg, 'Duplicate entry')) {
                        $errors[] = substr($msg, 0, 150);
                    }
                }
            }
            $buffer = '';
        }
    }

    $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
    
    // Count rows
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    $counts = [];
    foreach ($tables as $t) {
        $counts[$t] = $pdo->query("SELECT COUNT(*) FROM `$t`")->fetchColumn();
    }

    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'errors_count' => count($errors),
        'errors' => array_slice($errors, 0, 10),
        'table_counts' => $counts
    ]);
} catch (Exception $e) {
    echo json_encode(['fatal' => $e->getMessage()]);
}
