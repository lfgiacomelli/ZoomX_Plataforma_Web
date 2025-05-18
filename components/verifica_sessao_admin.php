<?php
require_once __DIR__ . '/../routes/config.php'; 

ini_set('session.save_path', '/tmp');

session_start();

if (
    !isset($_SESSION['logado099']) ||
    !isset($_SESSION['tipo']) ||
    $_SESSION['tipo'] !== 'atendente'
) {
    header('Location: ' . BASE_URL . 'admin/login.php');
    exit;
}
