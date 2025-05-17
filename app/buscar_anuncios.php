<?php
require '../bd/conexao.php';

// Obtém a instância de conexão
$conexao = conexao::getInstance();

try {
    $query = 'SELECT * FROM anuncios';
    $stmt = $conexao->prepare($query);
    $stmt->execute();

    $anuncios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($anuncios) {
        header('Content-Type: application/json');
        echo json_encode($anuncios);
    } else {
        // Caso não haja anúncios, retorna um array vazio
        header('Content-Type: application/json');
        echo json_encode([]);
    }

} catch (PDOException $e) {
    // Caso ocorra um erro na execução da consulta
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Erro ao buscar os anúncios: ' . $e->getMessage()]);
}
