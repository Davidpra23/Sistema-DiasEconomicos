<?php
$config = require __DIR__ . '/config.php';

// Clean up path
$dbPath = realpath($config['db_path']);
if (!$dbPath || !file_exists($dbPath)) {
    // Fallback to direct path usage if realpath fails (e.g. permission issues on parent)
    $dbPath = $config['db_path'];
}

$dsn = "sqlite:{$dbPath}";

try {
    $pdo = new PDO($dsn, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    // Enable foreign keys for SQLite
    $pdo->exec('PRAGMA foreign_keys = ON');
}
catch (PDOException $e) {
    // Log error to file for debugging
    file_put_contents(__DIR__ . '/error_log.txt', date('Y-m-d H:i:s') . " - DB Error: " . $e->getMessage() . "\n", FILE_APPEND);

    http_response_code(500);
    echo json_encode(['error' => 'DB connection failed', 'message' => $e->getMessage()]);
    exit;
}

return $pdo;
