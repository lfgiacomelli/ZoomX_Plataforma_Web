<?php
session_start();

if (
    !isset($_SESSION['logado099']) ||
    !isset($_SESSION['tipo']) ||
    $_SESSION['tipo'] !== 'usuario'
) {
       header('Location: ' . BASE_URL .'user/login.php');

    exit;
}
