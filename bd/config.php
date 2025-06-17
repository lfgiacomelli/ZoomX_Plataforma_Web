<?php
require_once '../envloader.php';
loadEnv(__DIR__ . '/.env');

$databaseUrl = getenv('DATABASE_URL');
$dbparts = parse_url($databaseUrl);

define('LOCAL_PATH', getenv('LOCAL_PATH'));
define('HOST', $dbparts['host'] ?? 'localhost');
define('PORT', $dbparts['port'] ?? '5432');
define('DBNAME', ltrim($dbparts['path'], '/') ?? '');
define('USER', $dbparts['user'] ?? '');
define('PASSWORD', $dbparts['pass'] ?? '');
define('CHARSET', 'utf8');

// Criar a conexão PDO (exemplo)
try {
    $dsn = "pgsql:host=" . HOST . ";port=" . PORT . ";dbname=" . DBNAME . ";";
    $pdo = new PDO($dsn, USER, PASSWORD, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}
