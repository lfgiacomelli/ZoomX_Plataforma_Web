<?php
require_once '/bd/conexao.php';

try {
    $pdo = Conexao::getInstance();
    echo "Conexão bem-sucedida!";
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
