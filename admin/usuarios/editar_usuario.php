<?php


session_start();

if (!isset($_SESSION["logado099"]) || !isset($_SESSION["tipo"]) || $_SESSION["tipo"] !== 'atendente') {
    header("Location: ../index.php");
    exit;
}