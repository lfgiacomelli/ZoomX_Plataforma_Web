<?php

require '../../bd/conexao.php';
$conexao = conexao::getInstance();

include '../../components/verifica_sessao_admin.php';

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
if (!$id) {
    header('Location: usuarios.php');
    exit;
}

$sql = 'SELECT * FROM usuarios WHERE usu_codigo = :id';
$stmt = $conexao->prepare($sql);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmar Exclusão</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts - Righteous -->
    <link href="https://fonts.googleapis.com/css2?family=Righteous&display=swap" rel="stylesheet">

    <style>
        body {
            background-color: #f0f0f0;
            font-family: 'Righteous', cursive;
            color: #000;
        }

        .card {
            background-color: #fff;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .btn-excluir {
            background-color: #000;
            color: #fff;
            border: none;
        }

        .btn-excluir:hover {
            background-color: #333;
        }

        .btn-cancelar {
            color: #000;
            text-decoration: underline;
        }

        h1 {
            font-size: 2rem;
            margin-bottom: 1.5rem;
        }

        .container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        @media (max-width: 576px) {
            .card {
                padding: 1.5rem;
            }

            h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body>

    <?php include '../../components/header_admin.php' ?>

    <div class="container">
        <div class="card text-center w-100" style="max-width: 500px;">
            <h1>Deseja realmente excluir <br><strong><?= htmlspecialchars($usuario['usu_nome']) ?></strong>?</h1>
            <form action="../../actions/actionusuario_admin.php" method="post" class="d-grid gap-3">
                <input type="hidden" name="id" value="<?= $usuario['usu_codigo'] ?>">
                <input type="hidden" name="acao" value="excluir">
                <button type="submit" class="btn btn-excluir">Sim, excluir</button>
                <a href="usuarios.php" class="btn btn-link btn-cancelar">Cancelar</a>
            </form>
        </div>
    </div>

</body>

</html>
