<?php
require '../bd/conexao.php';
$conexao = conexao::getInstance();

header('Content-Type: application/json');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'ID inválido']);
    exit;
}

$sql = "SELECT s.sol_status, f.fun_nome, m.mot_modelo, m.mot_placa
        FROM solicitacoes s 
        LEFT JOIN viagens v ON s.sol_codigo = v.sol_codigo 
        LEFT JOIN funcionarios f ON v.fun_codigo = f.fun_codigo 
        LEFT JOIN motocicletas m ON f.fun_codigo = m.fun_codigo 
        WHERE s.sol_codigo = :id LIMIT 1";

$stmt = $conexao->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$solicitacao = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$solicitacao) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Solicitação não encontrada']);
    exit;
}

$status = strtolower($solicitacao['sol_status']);
$fun_nome = $solicitacao['fun_nome'] ?? null;
$motocicleta = $solicitacao['mot_modelo'] ?? null;
$mot_placa = $solicitacao['mot_placa'] ?? null;

if ($status === 'pendente') {
    echo json_encode(['status' => 'pendente']);
} else {
    $mensagem = $status === 'aceita' ? "Sua solicitação foi {$status}." : 'Sua solicitação foi recusada.';
    
    $response = [
        'status' => $status,
        'mensagem' => $mensagem
    ];
    
    if ($fun_nome) $response['fun_nome'] = $fun_nome;
    if ($motocicleta) $response['mot_modelo'] = $motocicleta;
    if ($mot_placa) $response['mot_placa'] = $mot_placa;
    
    echo json_encode($response);
}