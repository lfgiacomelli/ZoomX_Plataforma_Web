<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once '../bd/conexao.php';

$conexao = conexao::getInstance();
$data = json_decode(file_get_contents("php://input"));

$email = $data->usu_email ?? '';
$senha = $data->usu_senha ?? '';

if (!$email || !$senha) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Informe o email e a senha']);
    exit;
}

try {
    $sql = "SELECT * FROM usuarios WHERE usu_email = :email";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($senha, $usuario['usu_senha'])) {
        echo json_encode([
            'status' => 'sucesso',
            'mensagem' => 'Login efetuado com sucesso',
            'usuario' => [
                'id' => $usuario['id'],
                'email' => $usuario['usu_email']
            ]
        ]);
    } else {
        echo json_encode(['status' => 'erro', 'mensagem' => 'Email ou senha inválidos']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Erro no servidor']);
}
