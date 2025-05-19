<?php
#include '../components/verifica_sessao.php';
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

if (
    $acao === 'editar' &&
    isset($_SESSION['logado099'], $_SESSION['tipo']) &&
    $_SESSION['logado099'] === true &&
    $_SESSION['tipo'] === 'usuario'
) {
    if (!empty($senha)) {
        $hashedPassword = password_hash($senha, PASSWORD_DEFAULT);  // Cria o hash da senha
        $sql = "UPDATE usuarios SET usu_nome = :nome, usu_email = :email, usu_senha = :senha, usu_telefone = :telefone, usu_ativo = :ativo, usu_updated_at = :updated_at WHERE usu_codigo = :id";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':senha', $hashedPassword); 
    } else {
        $sql = "UPDATE usuarios SET usu_nome = :nome, usu_email = :email, usu_telefone = :telefone, usu_ativo = :ativo, usu_updated_at = :updated_at WHERE usu_codigo = :id";
        $stmt = $conexao->prepare($sql);
    }

    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':telefone', $telefone);
    $stmt->bindParam(':ativo', $ativo);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':updated_at', $updated_at);
    $retorno = $stmt->execute();

    if ($retorno) {
        $_SESSION['mensagem'] = 'Suas informações foram editadas com sucesso!';
        header('Location: ../user/conta.php');
        exit;
    } else {
        echo "<div class='alert alert-danger' role='alert'>Erro ao editar suas informações!</div>";
        header('Location: ../user/conta.php');
        exit;
    }
}

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
    $acao === 'excluir' &&
    isset($_SESSION['logado099'], $_SESSION['tipo']) &&
    $_SESSION['logado099'] === true &&
    ($_SESSION['tipo'] === 'usuario' || $_SESSION['tipo'] === 'atendente')
) {
    try {
        $sql = 'DELETE FROM avaliacoes WHERE via_codigo IN (
                    SELECT via_codigo FROM viagens WHERE usu_codigo = :id
                )';
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $sql = 'DELETE FROM viagens WHERE usu_codigo = :id';
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $sql = 'DELETE FROM avaliacoes WHERE usu_codigo = :id';
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $sql = 'DELETE FROM solicitacoes WHERE usu_codigo = :id';
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $sql = "DELETE FROM usuarios WHERE usu_codigo = :id";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $retorno = $stmt->execute();

        if ($retorno) {
            if ($_SESSION['tipo'] === 'usuario') {
                $_SESSION = array();
                session_destroy();

                header('Location: ../index.php');
                exit;
            } elseif ($_SESSION['tipo'] === 'atendente') {
                $_SESSION['mensagem'] = 'Conta excluída com sucesso!';
                header('Location: ../admin/usuarios/usuarios.php');
                exit;
            }
        } else {
            $_SESSION['mensagem'] = 'Erro ao excluir conta!';
            if ($_SESSION['tipo'] === 'atendente') {
                header('Location: ../admin/usuarios/usuarios.php');
            } else {
                header('Location: ../index.php');
            }
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['mensagem'] = 'Erro no banco de dados: ' . $e->getMessage();
        if ($_SESSION['tipo'] === 'atendente') {
            header('Location: ../admin/usuarios/usuarios.php');
        } else {
            header('Location: ../index.php');
        }
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
