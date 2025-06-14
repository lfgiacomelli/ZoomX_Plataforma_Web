<?php

include '../components/verifica_sessao_admin.php';

require '../bd/conexao.php';
$conexao = conexao::getInstance();

$sql = "SELECT s.sol_codigo, s.sol_origem, s.sol_destino, s.sol_valor, s.sol_servico, s.sol_formapagamento,
               s.sol_data, u.usu_nome, u.usu_codigo, u.usu_telefone
        FROM solicitacoes s 
        INNER JOIN usuarios u ON u.usu_codigo = s.usu_codigo
        WHERE s.sol_status = 'Pendente' OR s.sol_status = 'pendente' 
        ORDER BY s.sol_data DESC";
$stmt = $conexao->prepare($sql);
$stmt->execute();
$solicitacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT f.fun_codigo, f.fun_nome, f.fun_telefone, m.mot_codigo, m.mot_modelo
        FROM funcionarios f
        LEFT JOIN motocicletas m ON f.fun_codigo = m.fun_codigo
        WHERE f.fun_ativo = true AND f.fun_cargo = 'Mototaxista'
        ORDER BY f.fun_nome";
$mototaxistas = $conexao->query($sql)->fetchAll(PDO::FETCH_ASSOC);

$mototaxistasSemMoto = array_filter($mototaxistas, function ($m) {
    return empty($m['mot_codigo']);
});
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Atendente | Solicitações Pendentes</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../css/solicitacoes.css">
</head>

<body>
    <?php include '../components/header_admin.php'; ?>

    <div class="container-fluid">
        <div class="header">
            <h1><i class="fas fa-clock"></i> Solicitações Pendentes</h1>
            <div class="header-actions">
                <span class="badge badge-primary">
                    <?= count($solicitacoes) ?> <?= count($solicitacoes) === 1 ? 'solicitação' : 'solicitações' ?>
                </span>
                <?php if (!empty($mototaxistasSemMoto)): ?>
                    <span class="badge badge-warning">
                        <?= count($mototaxistasSemMoto) ?> mototaxista(s) sem motocicleta
                        <a href="motocicletas/adicionar_motocicleta.php" class="btn" id="add-mototaxista">
                            <i class="fas fa-motorcycle"></i> Cadastrar
                        </a>
                    </span>

                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($_SESSION['mensagem'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?= htmlspecialchars($_SESSION['mensagem']); ?>
                <?php unset($_SESSION['mensagem']); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h2 class="card-title"><i class="fas fa-list"></i> Lista de Solicitações</h2>
                <div>
                    <button class="btn btn-primary btn-sm" onclick="atualizarLista()">
                        <i class="fas fa-sync-alt"></i> Atualizar
                    </button>
                </div>
            </div>

            <?php if (count($solicitacoes) === 0): ?>
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="far fa-check-circle"></i>
                    </div>
                    <h3 class="empty-state-title">Nenhuma solicitação pendente</h3>
                    <p class="empty-state-description">Todas as solicitações foram processadas. Novas solicitações
                        aparecerão aqui automaticamente.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Trajeto</th>
                                <th>Valor</th>
                                <th>Serviço</th>
                                <th>Pagamento</th>
                                <th>Horário</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($solicitacoes as $solicitacao): ?>
                                <tr>
                                    <td>#<?= str_pad($solicitacao['sol_codigo'], 5, '0', STR_PAD_LEFT) ?></td>
                                    <td>
                                        <div class="user-info">
                                            <div class="user-avatar">
                                                <?= strtoupper(substr($solicitacao['usu_nome'], 0, 1)) ?>
                                            </div>
                                            <div class="user-details">
                                                <div class="user-name"><?= htmlspecialchars($solicitacao['usu_nome']) ?></div>
                                                <div class="user-phone"><?= htmlspecialchars($solicitacao['usu_telefone']) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="solicitacao-info">
                                            <div class="solicitacao-origem">
                                                <i class="fas fa-map-marker-alt"></i>
                                                <span><?= htmlspecialchars($solicitacao['sol_origem']) ?></span>
                                            </div>
                                            <div class="solicitacao-destino">
                                                <i class="fas fa-flag-checkered"></i>
                                                <span><?= htmlspecialchars($solicitacao['sol_destino']) ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="valor">R$ <?= number_format($solicitacao['sol_valor'], 2, ',', '.') ?></td>
                                    <td>
                                        <span
                                            class="servico"><?= ucfirst(htmlspecialchars($solicitacao['sol_servico'])) ?></span>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($solicitacao['sol_formapagamento']) ?>
                                    </td>
                                    <td>
                                        <span class="data" title="<?= date('H:i', strtotime($solicitacao['sol_data'])) ?>">
                                            <?= date('H:i', strtotime($solicitacao['sol_data'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <form class="action-form" method="POST" action="../actions/actionsolicitacao_admin.php"
                                            onsubmit="return validarFormulario(this)">
                                            <input type="hidden" name="id_solicitacao"
                                                value="<?= $solicitacao['sol_codigo'] ?>">

                                            <select class="js-mototaxista-select" name="funcionario_codigo" style="width: 100%;"
                                                required>
                                                <option value="">Selecione um mototaxista</option>
                                                <?php foreach ($mototaxistas as $mototaxista): ?>
                                                    <option value="<?= $mototaxista['fun_codigo'] ?>"
                                                        <?= empty($mototaxista['mot_codigo']) ? 'data-warning="1"' : '' ?>
                                                        data-telefone="<?= htmlspecialchars($mototaxista['fun_telefone']) ?>"
                                                        data-moto="<?= htmlspecialchars($mototaxista['mot_modelo'] ?? 'Não cadastrada') ?>">
                                                        <?= htmlspecialchars($mototaxista['fun_nome']) ?>
                                                        <?= empty($mototaxista['mot_codigo']) ? ' (sem moto)' : '' ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>

                                            <div class="btn-group">
                                                <button type="submit" name="acao" value="aceitar" class="btn btn-success"
                                                    onclick="marcarBotao(this)">
                                                    <i class="fas fa-check"></i> Aceitar
                                                </button>
                                                <button type="submit" name="acao" value="recusar" class="btn btn-danger"
                                                    onclick="marcarBotao(this)">
                                                    <i class="fas fa-times"></i> Recusar
                                                </button>

                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function () {
            aplicarSelect2();

            <?php if (!empty($mototaxistasSemMoto)): ?>
                Swal.fire({
                    icon: 'warning',
                    title: 'Atenção',
                    html: 'Existem <?= count($mototaxistasSemMoto) ?> mototaxista(s) sem motocicleta cadastrada. <br><br>Esses mototaxistas não poderão atender solicitações.',
                    confirmButtonText: 'Entendi',
                    confirmButtonColor: '#4361ee'
                });
            <?php endif; ?>

            setTimeout(atualizarLista, 30000);
        });

        function aplicarSelect2() {
            $('.js-mototaxista-select').select2({
                placeholder: "Selecione um mototaxista",
                allowClear: true,
                templateResult: formatMototaxista,
                templateSelection: formatMototaxistaSelection
            });
        }

        function formatMototaxista(mototaxista) {
            if (!mototaxista.id) return mototaxista.text;

            const $container = $(`
            <div class="mototaxista-option">
                <span class="mototaxista-name"></span>
                <div class="mototaxista-details"></div>
            </div>
        `);

            $container.find('.mototaxista-name').text(mototaxista.text);

            const telefone = $(mototaxista.element).data('telefone');
            const moto = $(mototaxista.element).data('moto');
            const warning = $(mototaxista.element).data('warning');

            let details = `<small class="text-muted">Tel: ${telefone} | Moto: ${moto}</small>`;

            if (warning) {
                details += '<span class="status-badge ml-2"><span class="status-dot dot-warning"></span><small class="text-warning">Sem motocicleta</small></span>';
            }

            $container.find('.mototaxista-details').html(details);

            return $container;
        }

        function formatMototaxistaSelection(mototaxista) {
            if (!mototaxista.id) return mototaxista.text;

            const warning = $(mototaxista.element).data('warning');
            let text = mototaxista.text;

            if (warning) {
                text += ' ⚠️';
            }

            return text;
        }

        function atualizarLista() {
            $.ajax({
                url: window.location.href,
                method: 'GET',
                success: function (data) {
                    const $newData = $(data).find('.table-responsive').html();
                    $('.table-responsive').html($newData);

                    aplicarSelect2();
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer);
                            toast.addEventListener('mouseleave', Swal.resumeTimer);
                        }
                    });

                    Toast.fire({
                        icon: 'success',
                        title: 'Lista atualizada com sucesso'
                    });
                },
                complete: function () {
                    setTimeout(atualizarLista, 30000);
                }
            });
        }

        function validarFormulario(form) {
            const $form = $(form);
            const clickedButton = $form.find('button[clicked="true"]');
            const acao = clickedButton.val();
            const select = $form.find('select[name="funcionario_codigo"]');

            if (acao === 'aceitar' && !select.val()) {
                Swal.fire({
                    icon: 'error',
                    title: 'Ops...',
                    text: 'Você precisa selecionar um mototaxista para aceitar a solicitação!',
                    confirmButtonColor: '#4361ee'
                });
                return false;
            }

            if (acao === 'aceitar' && select.find('option:selected').data('warning')) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Atenção',
                    html: 'O mototaxista selecionado não tem uma motocicleta cadastrada. <br><br>Deseja continuar mesmo assim?',
                    showCancelButton: true,
                    confirmButtonText: 'Sim, continuar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#4361ee',
                    cancelButtonColor: '#f72585'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
                return false;
            }

            return true;
        }

        setInterval(() => {
            window.location.reload();
        }, 12000);
        function marcarBotao(botao) {
            const $form = $(botao).closest('form');
            $form.find('button[type="submit"]').removeAttr('clicked');
            $(botao).attr('clicked', 'true');
        }
    </script>
</body>

</html>