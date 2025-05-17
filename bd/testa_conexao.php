<?php
require_once 'conexao.php';

try {
    $pdo = Conexao::getInstance();
    echo "Conexão bem-sucedida!";
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
