<?php
declare(strict_types=1);

// Set DB_DRIVER=pgsql for Supabase. MySQL remains the local-development default.
$dbDriver = strtolower(getenv('DB_DRIVER') ?: 'mysql');
$dbHost = getenv('DB_HOST') ?: 'localhost';
$dbName = getenv('DB_NAME') ?: 'socom_dtr';
$dbUser = getenv('DB_USER') ?: 'root';
$dbPass = getenv('DB_PASS') ?: '';
$dbPort = getenv('DB_PORT') ?: ($dbDriver === 'pgsql' ? '5432' : '3306');

if (!in_array($dbDriver, ['mysql', 'pgsql'], true)) {
    throw new RuntimeException('DB_DRIVER must be mysql or pgsql.');
}

try {
    $dsn = $dbDriver . ':host=' . $dbHost . ';port=' . $dbPort . ';dbname=' . $dbName;
    if ($dbDriver === 'pgsql') {
        $dsn .= ';sslmode=require';
    }

    $pdo = new PDO(
        $dsn,
        $dbUser,
        $dbPass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    die('Database connection failed. Please check config/db.php and ensure the database is set up (see database.sql).');
}
