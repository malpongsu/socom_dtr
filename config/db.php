<?php
declare(strict_types=1);

// Set DB_DRIVER=pgsql for Supabase. Credentials must come from the environment.
$dbDriver = strtolower(getenv('DB_DRIVER') ?: 'mysql');
$dbHost = getenv('DB_HOST') ?: '';
$dbName = getenv('DB_NAME') ?: '';
$dbUser = getenv('DB_USER') ?: '';
$dbPass = getenv('DB_PASSWORD') ?: (getenv('DB_PASS') ?: '');
$dbPort = getenv('DB_PORT') ?: ($dbDriver === 'pgsql' ? '6543' : '3306');

if (!in_array($dbDriver, ['mysql', 'pgsql'], true)) {
    throw new RuntimeException('DB_DRIVER must be mysql or pgsql.');
}

if ($dbDriver === 'pgsql' && !extension_loaded('pdo_pgsql')) {
    http_response_code(500);
    die('The PHP runtime does not have the pdo_pgsql driver. Use a PHP runtime with PostgreSQL PDO support or connect through a Node/Supabase API.');
}

if ($dbHost === '' || $dbName === '' || $dbUser === '' || $dbPass === '') {
    http_response_code(500);
    die('Database variables are missing. Set DB_DRIVER, DB_HOST, DB_PORT, DB_NAME, DB_USER, and DB_PASSWORD in Vercel.');
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
    error_log('Database connection failed: ' . $e->getMessage());
    http_response_code(500);
    die('Database connection failed. Check the Vercel DB_* variables and the Supabase Transaction pooler credentials. See the Vercel function logs for the technical error.');
}
