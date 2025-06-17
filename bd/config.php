<?php
define('LOCAL_PATH', getenv('LOCAL_PATH') ?: 'https://zoomx.onrender.com/');

$databaseUrl = getenv('DATABASE_URL') ?: 'postgresql://giacomelli_devs:NyO6nehZ5tWBFopexVOJrpvCalF0y2ZS@dpg-d18bupggjchc73ep07vg-a.oregon-postgres.render.com:5432/zoomx_tcc_fx7z';

$dbparts = parse_url($databaseUrl);

define('HOST', $dbparts['host'] ?? 'localhost');
define('PORT', $dbparts['port'] ?? '5432');
define('DBNAME', ltrim($dbparts['path'], '/') ?? 'zoomx_tcc_fx7z');
define('USER', $dbparts['user'] ?? 'giacomelli_devs');
define('PASSWORD', $dbparts['pass'] ?? 'NyO6nehZ5tWBFopexVOJrpvCalF0y2ZS');
define('CHARSET', 'utf8');
