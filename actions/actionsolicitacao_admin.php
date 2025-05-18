<?php
session_start();
if (!isset($_SESSION["logado099"]) && $_SESSION["tipo"] !== 'atendente') {
    header("Location: ../index.php");
    exit;
}
require '../bd/conexao.php';
$conexao = conexao::getInstance();

$acao = $_POST['acao'] ?? null;
$via_codigo = isset($_POST['via_codigo']) ? (int) $_POST['via_codigo'] : 0;
$id_solicitacao = isset($_POST['id_solicitacao']) ? (int) $_POST['id_solicitacao'] : 0;
$status = $_POST['status'] ?? ($acao === 'aceitar' ? 'aceita' : 'recusada');
$funcionario_codigo = $_POST['funcionario_codigo'] ?? null;
$ate_codigo = $_SESSION['fun_codigo'] ?? null;  

    if ($acao === 'finalizar' && $via_codigo > 0) {
        try {
            $conexao->beginTransaction();

            $sqlBuscarDados = "
                SELECT v.fun_codigo, v.usu_codigo, u.usu_email, u.usu_nome
                FROM viagens v
                INNER JOIN usuarios u ON v.usu_codigo = u.usu_codigo
                WHERE v.via_codigo = :via_codigo
                LIMIT 1";
            
            $stmt = $conexao->prepare($sqlBuscarDados);
            $stmt->bindParam(':via_codigo', $via_codigo, PDO::PARAM_INT);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$resultado || !isset($resultado['fun_codigo'], $resultado['usu_email'])) {
                throw new Exception('Funcionário ou usuário não encontrado para esta viagem.');
            }

            $funcionario_codigo = $resultado['fun_codigo'];
            $emailUsuario = $resultado['usu_email'];
            $nomeUsuario = $resultado['usu_nome'] ?? 'Cliente';

            $sqlAtivar = "UPDATE funcionarios SET fun_ativo = true WHERE fun_codigo = :funcionario_codigo";
            $stmt = $conexao->prepare($sqlAtivar);
            $stmt->bindParam(':funcionario_codigo', $funcionario_codigo, PDO::PARAM_INT);
            $stmt->execute();

            $sqlAtualizarViagem = "UPDATE viagens SET via_status = 'finalizada' WHERE via_codigo = :via_codigo";
            $stmt = $conexao->prepare($sqlAtualizarViagem);
            $stmt->bindParam(':via_codigo', $via_codigo, PDO::PARAM_INT);
            $stmt->execute();

            $avaliarUrl = "https://zoomx.onrender.com/user/avaliar_viagem.php?via_codigo=" . urlencode($via_codigo);
            
            $assunto = "Viagem Finalizada - ZoomX";
            
            $mensagem = "
                <!DOCTYPE html>
                <html lang='pt-BR'>
                <head>
                    <meta charset='UTF-8'>
                    <title>Viagem Finalizada</title>
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                        .header { background-color: #0066cc; color: white; padding: 10px; text-align: center; }
                        .content { padding: 20px; background-color: #f9f9f9; }
                        .button { 
                            display: inline-block; padding: 10px 20px; 
                            background-color: #0066cc; color: white; 
                            text-decoration: none; border-radius: 5px; 
                        }
                        .footer { margin-top: 20px; font-size: 12px; color: #777; }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <div class='header'>
                            <h1>ZoomX</h1>
                        </div>
                        <div class='content'>
                            <p>Olá, {$nomeUsuario}</p>
                            <p>Sua viagem com código <strong>{$via_codigo}</strong> foi finalizada com sucesso.</p>
                            <p>Agradecemos por utilizar nossos serviços e gostaríamos de saber sua opinião.</p>
                            <p>
                                <a href='{$avaliarUrl}' class='button'>Avaliar Viagem</a>
                            </p>
                            <p>Se o botão não funcionar, copie e cole este link em seu navegador:<br>
                            <small>{$avaliarUrl}</small></p>
                        </div>
                        <div class='footer'>
                            <p>Este é um e-mail automático, por favor não responda.</p>
                            <p>&copy; " . date('Y') . " ZoomX. Todos os direitos reservados.</p>
                        </div>
                    </div>
                </body>
                </html>
            ";

            $headers = [
                'From: ZoomX <no-reply@zoomx.com.br>',
                'Reply-To: suporte@zoomx.com.br',
                'MIME-Version: 1.0',
                'Content-type: text/html; charset=UTF-8',
                'X-Mailer: PHP/' . phpversion(),
                'X-Priority: 1 (Highest)',
                'X-MSMail-Priority: High',
                'Importance: High'
            ];
            
            $headers = implode("\r\n", $headers);

            $emailEnviado = mail($emailUsuario, $assunto, $mensagem, $headers);
            
            if (!$emailEnviado) {
                throw new Exception('Falha ao enviar e-mail de confirmação.');
            }

            $conexao->commit();
            
            $_SESSION['mensagem'] = [
                'tipo' => 'success',
                'texto' => 'Viagem finalizada com sucesso e e-mail enviado ao usuário!'
            ];
            
        } catch (PDOException $e) {
            if ($conexao->inTransaction()) {
                $conexao->rollBack();
            }
            
            $_SESSION['mensagem'] = [
                'tipo' => 'error',
                'texto' => 'Erro no banco de dados ao finalizar viagem: ' . $e->getMessage()
            ];
            
        } catch (Exception $e) {
            if ($conexao->inTransaction()) {
                $conexao->rollBack();
            }
            
            $_SESSION['mensagem'] = [
                'tipo' => 'warning',
                'texto' => $e->getMessage()
            ];
        }

        header('Location: ../admin/index.php');
        exit;
    }



if (!in_array($acao, ['aceitar', 'recusar']) || $id_solicitacao <= 0) {
    $_SESSION['mensagem'] = 'Ação inválida ou ID da solicitação ausente.';
    header('Location: ../admin/solicitacoes.php');
    exit;
}

try {
    $sql = "UPDATE solicitacoes SET sol_status = :status WHERE sol_codigo = :id_solicitacao";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':id_solicitacao', $id_solicitacao);
    $stmt->execute();
} catch (PDOException $e) {
    $_SESSION['mensagem'] = 'Erro ao atualizar status da solicitação: ' . $e->getMessage();
    header('Location: ../admin/solicitacoes.php');
    exit;
}
if ($acao === 'aceitar') {
    if (!$funcionario_codigo) {
        $_SESSION['mensagem'] = 'Você deve selecionar um mototaxista para aceitar a solicitação.';
        header('Location: ../admin/solicitacoes.php');
        exit;
    }

    try {
        $sql_solicitacao = "SELECT * FROM solicitacoes WHERE sol_codigo = :id_solicitacao LIMIT 1";
        $stmt_solicitacao = $conexao->prepare($sql_solicitacao);
        $stmt_solicitacao->bindParam(':id_solicitacao', $id_solicitacao);
        $stmt_solicitacao->execute();
        $solicitacao = $stmt_solicitacao->fetch(PDO::FETCH_ASSOC);

        if ($solicitacao) {
            $sql = 'UPDATE funcionarios set fun_ativo = true WHERE fun_codigo = :funcionario_codigo';
            $stmt = $conexao->prepare($sql);
            $stmt->bindParam(':funcionario_codigo', $funcionario_codigo);
            $stmt->execute();
            $sql_viagem = "INSERT INTO viagens (
                fun_codigo, sol_codigo, usu_codigo, ate_codigo, via_origem, via_destino, 
                via_valor, via_formapagamento, via_data, via_servico, via_status, via_observacoes
            ) VALUES (
                :fun_codigo, :sol_codigo, :usu_codigo, :ate_codigo, :via_origem, :via_destino, 
                :via_valor, :via_formapagamento, NOW(), :via_servico, 'em andamento', :via_observacoes
            )";

            $stmt_viagem = $conexao->prepare($sql_viagem);
            $stmt_viagem->bindParam(':fun_codigo', $funcionario_codigo);
            $stmt_viagem->bindParam(':sol_codigo', $id_solicitacao);
            $stmt_viagem->bindParam(':usu_codigo', $solicitacao['usu_codigo']);
            $stmt_viagem->bindParam(':ate_codigo', $ate_codigo);
            $stmt_viagem->bindParam(':via_origem', $solicitacao['sol_origem']);
            $stmt_viagem->bindParam(':via_destino', $solicitacao['sol_destino']);
            $stmt_viagem->bindParam(':via_valor', $solicitacao['sol_valor']);
            $stmt_viagem->bindParam(':via_formapagamento', $solicitacao['sol_formapagamento']);
            $stmt_viagem->bindParam(':via_servico', $solicitacao['sol_servico']);
            $stmt_viagem->bindParam(':via_observacoes', $solicitacao['sol_observacao']);
            $stmt_viagem->execute();
        }

        $_SESSION['mensagem'] = 'Solicitação aceita com sucesso!';
    } catch (PDOException $e) {
        $_SESSION['mensagem'] = 'Erro ao aceitar solicitação: ' . $e->getMessage();
    }

    header('Location: ../admin/solicitacoes.php');
    exit;
}

if ($acao === 'recusar') {
    $sql = "UPDATE solicitacoes SET sol_status = 'recusada' WHERE sol_codigo = :id_solicitacao";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':id_solicitacao', $id_solicitacao);
    $stmt->execute();
    $_SESSION['mensagem'] = 'Solicitação recusada com sucesso!';
    header('Location: ../admin/solicitacoes.php');
    exit;
}
