<?php
require_once __DIR__ . '/../routes/config.php'; 

session_start();

if (
    !isset($_SESSION['logado099']) ||
    !isset($_SESSION['tipo']) ||
    $_SESSION['tipo'] !== 'atendente'
) {
    header('Location: ' . BASE_URL .'admin/login.php');
    exit;
}
