<?php
set_time_limit(300);
ini_set('memory_limit', '512M');

$host = getenv('DB_HOST');
$port = getenv('DB_PORT') ?: 3306;
$db   = getenv('DB_DATABASE');
$user = getenv('DB_USERNAME');
$pass = getenv('DB_PASSWORD');

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_LOCAL_INFILE => true,
    ]);
    $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
    $pdo->exec("SET sql_mode=''");

    $sql = file_get_contents(__DIR__ . '/db_full.sql');
    
    // Use proper SQL splitting that handles multi-line strings
    $delimiter = ';';
    $buffer = '';
    $inString = false;
    $stringChar = '';
    $escaped = false;
    $success = 0;
    $errors = [];

    for ($i = 0; $i < strlen($sql); $i++) {
        $char = $sql[$i];
        
        if ($escaped) { $escaped = false; $buffer .= $char; continue; }
        if ($char === '\\' && $inString) { $escaped = true; $buffer .= $char; continue; }
        if (!$inString && ($char === '"' || $char === "'")) { $inString = true; $stringChar = $char; $buffer .= $char; continue; }
        if ($inString && $char === $stringChar) { $inString = false; $buffer .= $char; continue; }
        
        if (!$inString && $char === ';') {
            $stmt = trim($buffer);
            if (!empty($stmt) && !preg_match('/^(--|#|\/\*)/', $stmt)) {
                try {
                    $pdo->exec($stmt);
                    $success++;
                } catch (Exception $e) {
                    $errors[] = substr($e->getMessage(), 0, 100);
                }
            }
            $buffer = '';
        } else {
            $buffer .= $char;
        }
    }

    $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
    
    echo json_encode([
        'success' => $success,
        'errors_count' => count($errors),
        'sample_errors' => array_slice($errors, 0, 5)
    ]);
} catch (Exception $e) {
    echo json_encode(['fatal' => $e->getMessage()]);
}
