<?php
define('LOCAL_PATH', getenv('LOCAL_PATH') ?: 'https://https://zoomx.onrender.com/');

$databaseUrl = getenv('DATABASE_URL') ?: 'postgresql://smithgg415:Jtn5fpob64g18cD9hlsZ6cXHPtoK6jTd@dpg-d0kgkoruibrs739hd8f0-a.oregon-postgres.render.com:5432/zoomx_tcc';

$dbparts = parse_url($databaseUrl);

define('HOST', $dbparts['host'] ?? 'localhost');
define('PORT', $dbparts['port'] ?? '5432');
define('DBNAME', ltrim($dbparts['path'], '/') ?? 'zoomx_tcc');
define('USER', $dbparts['user'] ?? 'postgres');
define('PASSWORD', $dbparts['pass'] ?? 'Jtn5fpob64g18cD9hlsZ6cXHPtoK6jTd');
define('CHARSET', 'utf8');
?>
