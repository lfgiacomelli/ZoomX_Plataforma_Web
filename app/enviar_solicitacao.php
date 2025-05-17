<?php
// Inicia a sessão e verifica se o usuário está logado
// session_start();
// if (!isset($_SESSION['logado099']) || $_SESSION['ativo'] != 1 || $_SESSION['tipo'] !== 'usuario') {
//     echo json_encode(['status' => 'erro', 'mensagem' => 'Usuário não autenticado']);
//     exit;
// }

require '../bd/conexao.php';
$conexao = conexao::getInstance();

// Recebe os dados do corpo da requisição JSON
$data = json_decode(file_get_contents('php://input'), true);

$usuario_id = isset($data['usu_codigo']) ? $data['usu_codigo'] : 0;
$origem = isset($data['origem']) ? $data['origem'] : null;
$destino = isset($data['destino']) ? $data['destino'] : null;
$valor = isset($data['valor']) ? $data['valor'] : 0;
$formapagamento = isset($data['forma_pagamento']) ? $data['forma_pagamento'] : null;
$distancia = isset($data['distancia']) ? $data['distancia'] : 0;
$largura = isset($data['largura']) ? $data['largura'] : null;
$comprimento = isset($data['comprimento']) ? $data['comprimento'] : null;
$peso = isset($data['peso']) ? $data['peso'] : null;
$servico = isset($data['servico']) ? $data['servico'] : null;
$observacao = isset($data['observacao']) ? $data['observacao'] : null;

if (!$origem || !$destino || !$valor || !$formapagamento || !$distancia || !$servico) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Campos obrigatórios não preenchidos']);
    exit;
}

if (isset($data['acao']) && $data['acao'] == 'adicionar') {
    try {
        $sql = "INSERT INTO solicitacoes (sol_origem, sol_destino, sol_valor, sol_formapagamento, sol_distancia, sol_data, usu_codigo, sol_largura, sol_comprimento, sol_peso, sol_status, sol_servico, sol_observacoes) 
                VALUES (:origem, :destino, :valor, :formapagamento, :distancia, NOW(), :usuario_id, :largura, :comprimento, :peso, 'pendente', :servico, :observacao)";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':origem', $origem);
        $stmt->bindParam(':destino', $destino);
        $stmt->bindParam(':valor', $valor);
        $stmt->bindParam(':formapagamento', $formapagamento);
        $stmt->bindParam(':distancia', $distancia);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->bindParam(':largura', $largura);
        $stmt->bindParam(':comprimento', $comprimento);
        $stmt->bindParam(':peso', $peso);
        $stmt->bindParam(':servico', $servico);
        $stmt->bindParam(':observacao', $observacao);

        $retorno = $stmt->execute();

        if ($retorno) {
            echo json_encode(['status' => 'sucesso', 'mensagem' => 'Solicitação realizada com sucesso!', 'solicitacao_id' => $conexao->lastInsertId()]);
        } else {
            echo json_encode(['status' => 'erro', 'mensagem' => 'Erro ao realizar solicitação']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'erro', 'mensagem' => 'Erro ao inserir dados: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Ação inválida']);
}
?>
