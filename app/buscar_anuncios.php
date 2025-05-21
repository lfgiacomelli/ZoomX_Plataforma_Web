<?php
require '../bd/conexao.php';

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
        header('Content-Type: application/json');
        echo json_encode([]);
    }

} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Erro ao buscar os anúncios: ' . $e->getMessage()]);
}
