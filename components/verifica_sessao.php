<?php
require_once __DIR__ . '/../routes/config.php'; 

ini_set('session.save_path', '/tmp');

session_start();
if (
    empty($_SESSION['logado099']) ||
    $_SESSION['tipo'] !== 'usuario'
) {
    header('Location: ' . BASE_URL . 'user/login.php');
    exit;
}
