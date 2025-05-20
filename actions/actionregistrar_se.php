<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../user/registrar_se.php');
    exit;
}

require '../bd/conexao.php';
$conexao = conexao::getInstance();

$acao = isset($_POST['acao']) ? $_POST['acao'] : null;
$id = isset($_POST['id']) ? $_POST['id'] : 0;
$nome = isset($_POST['nome']) ? $_POST['nome'] : null;
$email = isset($_POST['email']) ? $_POST['email'] : null;
$senha = isset($_POST['senha']) ? $_POST['senha'] : null;
$telefone = isset($_POST['telefone']) ? $_POST['telefone'] : null;
$ativo = isset($_POST['ativo']) ? $_POST['ativo'] : false;
$updated_at = date('Y-m-d H:i:s');
$created_at = date('Y-m-d H:i:s');
if ($acao == 'adicionar') {
    $sql_check = "SELECT usu_codigo FROM usuarios WHERE usu_email = :email";
    $stmt_check = $conexao->prepare($sql_check);
    $stmt_check->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt_check->execute();

    if ($stmt_check->rowCount() > 0) {
        $_SESSION['erro_email'] = "Este e-mail já está cadastrado em nosso sistema.";
        $_SESSION['form_data'] = $_POST;
        header("Location: ../user/registrar_se.php");
        exit();
    }

    $hashedPassword = password_hash($senha, PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuarios (usu_nome, usu_email, usu_senha, usu_telefone, usu_ativo, usu_created_at, usu_updated_at) 
            VALUES (:nome, :email, :senha, :telefone, :ativo, :created_at, :updated_at)";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':senha', $hashedPassword);
    $stmt->bindParam(':telefone', $telefone);
    $stmt->bindParam(':ativo', $ativo);
    $stmt->bindParam(':created_at', $created_at);
    $stmt->bindParam(':updated_at', $updated_at);
    $retorno = $stmt->execute();

    if ($retorno) {
        $usu_codigo = $conexao->lastInsertId();

        $_SESSION["logado099"] = true;
        $_SESSION['tipo'] = 'usuario';
        $_SESSION['ativo'] = true;
        $_SESSION["id"] = $usu_codigo;
        $_SESSION["nome"] = $nome;

        $_SESSION['mensagem'] = 'Conta criada com sucesso! Bem-vindo(a), ' . $nome . '!';
        header('Location: ../user/index.php');
        exit;
    } else {
        $_SESSION['erro'] = 'Erro ao criar conta!';
        header('Location: ../user/registrar_se.php');
        exit;
    }
}
