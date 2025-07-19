<?php
define('LOCAL_PATH', getenv('LOCAL_PATH') ?: 'https://zoomx.onrender.com/');

$databaseUrl = getenv('DATABASE_URL') ?: 'postgresql://giacomelli_devs:fqIvi8jKnQWe9hsFRUHZWMsEigT6TOwD@dpg-d1tgnl3ipnbc73cafgf0-a.oregon-postgres.render.com/zoomx_database';

$dbparts = parse_url($databaseUrl);

define('HOST', $dbparts['host'] ?? 'localhost');
define('PORT', $dbparts['port'] ?? '5432');
define('DBNAME', ltrim($dbparts['path'], '/') ?? 'zoomx_database');
define('USER', $dbparts['user'] ?? 'giacomelli_devs');
define('PASSWORD', $dbparts['pass'] ?? 'fqIvi8jKnQWe9hsFRUHZWMsEigT6TOwD');
define('CHARSET', 'utf8');
