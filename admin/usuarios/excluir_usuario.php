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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>deseja excluir <?= $usuario['usu_nome'] ?></h1>
    <form action="../../actions/actionusuario_admin.php" method="post">
        <input type="hidden" name="id" value="<?= $usuario['usu_codigo'] ?>">
        <input type="hidden" name="acao" value="excluir">
        <button type="submit">Excluir</button>
        <a href="usuarios.php">Cancelar</a>
    </form>
</body>
</html>
