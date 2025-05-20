<?php

include '../components/verifica_sessao_admin.php';
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


if (
    $acao === 'admin_editar' &&
    isset($_SESSION['logado099'], $_SESSION['tipo']) &&
    $_SESSION['logado099'] === true &&
    ($_SESSION['tipo'] === 'atendente')
) {
    if (!empty($senha)) {
        $hashedPassword = password_hash($senha, PASSWORD_DEFAULT);
        $sql = "UPDATE usuarios SET usu_nome = :nome, usu_email = :email, usu_senha = :senha, usu_telefone = :telefone, usu_ativo = :ativo WHERE usu_codigo = :id";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':senha', $hashedPassword);
    } else {
        $sql = "UPDATE usuarios SET usu_nome = :nome, usu_email = :email, usu_telefone = :telefone, usu_ativo = :ativo WHERE usu_codigo = :id";
        $stmt = $conexao->prepare($sql);
    }

    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':telefone', $telefone);
    $stmt->bindParam(':ativo', $ativo);
    $stmt->bindParam(':id', $id);
    $retorno = $stmt->execute();

    if ($retorno) {
        $_SESSION['mensagem'] = 'Usuário editado com sucesso!';
        header('Location: ../admin/usuarios/usuarios.php');
        exit;
    } else {
        echo "<div class='alert alert-danger' role='alert'>Erro ao editar usuário!</div>";
        header('Location: ../admin/usuarios/editar_usuario.php?id=' . $id);
        exit;
    }
}

if (
    $acao === 'banir' &&
    isset($_SESSION['logado099'], $_SESSION['tipo']) &&
    $_SESSION['logado099'] === true &&
    $_SESSION['tipo'] === 'atendente'
) {
    $sql = "UPDATE usuarios SET usu_ativo = false WHERE usu_codigo = :id";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':id', $id);
    $retorno = $stmt->execute();
    if ($retorno) {
        $_SESSION['mensagem'] = 'Usuário banido com sucesso!';
        header('Location: ../admin/usuarios/usuarios.php');
        exit;
    } else {
        echo "<div class='alert alert-danger' role='alert'>Erro ao banir usuário!</div>";
        header('Location: ../admin/usuarios/banir_usuario.php?id=' . $id);
        exit;
    }
}
if (
    $acao === 'desbanir' &&
    isset($_SESSION['logado099'], $_SESSION['tipo']) &&
    $_SESSION['logado099'] === true &&
    $_SESSION['tipo'] === 'atendente'
) {
    $sql = "UPDATE usuarios SET usu_ativo = true WHERE usu_codigo = :id";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':id', $id);
    $retorno = $stmt->execute();
    if ($retorno) {
        $_SESSION['mensagem'] = 'Usuário desbanido com sucesso!';
        header('Location: ../admin/usuarios/usuarios.php');
        exit;
    } else {
        echo "<div class='alert alert-danger' role='alert'>Erro ao desbanir usuário!</div>";
        header('Location: ../admin/usuarios/desbanir_usuario.php?id=' . $id);
        exit;
    }
}
