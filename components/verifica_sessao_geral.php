<?php
session_start();

if (!isset($_SESSION['logado099']) || !isset($_SESSION['tipo'])) {
    header('Location: ../user/login.php');
    exit;
}

$tipo = $_SESSION['tipo'];

if (!in_array($tipo, ['usuario', 'admin'])) {
    header('Location: ../user/login.php');
    exit;
}

