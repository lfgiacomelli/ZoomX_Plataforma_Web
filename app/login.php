<?php
header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ERROR | E_PARSE);

require_once(__DIR__ . '../bd/conexao.php');

$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);

$email = $input['usu_email'] ?? '';
$senha = $input['usu_senha'] ?? '';

if (!$email || !$senha) {
    echo json_encode([
        'status' => 'erro',
        'mensagem' => 'Informe o email e a senha'
    ]);
    exit;
}

$conexao = conexao::getInstance();

$query = "SELECT usu_email, usu_senha FROM usuarios WHERE usu_email = $1 LIMIT 1";
$result = pg_query_params($conexao, $query, [$email]);

if (!$result) {
    echo json_encode([
        'status' => 'erro',
        'mensagem' => 'Erro na consulta ao banco de dados'
    ]);
    exit;
}

if (pg_num_rows($result) === 0) {
    echo json_encode([
        'status' => 'erro',
        'mensagem' => 'Usuário não encontrado'
    ]);
    exit;
}

$row = pg_fetch_assoc($result);
$senhaBanco = $row['usu_senha'];

if (!password_verify($senha, $senhaBanco)) {
    echo json_encode([
        'status' => 'erro',
        'mensagem' => 'Senha incorreta'
    ]);
    exit;
}

// Se passou na validação
echo json_encode([
    'status' => 'sucesso',
    'usuario' => [
        'email' => $email
    ]
]);
