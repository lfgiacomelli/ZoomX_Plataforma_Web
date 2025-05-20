<?php
include '../components/verifica_sessao.php';


if (!isset($_SESSION['hora_abertura_' . $_GET['id']])) {
    $_SESSION['hora_abertura_' . $_GET['id']] = time();
}
$hora_abertura = $_SESSION['hora_abertura_' . $_GET['id']];

require '../bd/conexao.php';
$conexao = conexao::getInstance();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php?error=missing_id");
    exit;
}


$id_solicitacao = intval($_GET['id']);

$sql = "SELECT s.*, u.usu_nome, u.usu_email, u.usu_telefone, f.fun_nome, m.mot_modelo, m.mot_placa
        FROM solicitacoes s
        INNER JOIN usuarios u ON u.usu_codigo = s.usu_codigo
        LEFT JOIN viagens v ON s.sol_codigo = v.sol_codigo
        LEFT JOIN funcionarios f ON v.fun_codigo = f.fun_codigo
        LEFT JOIN motocicletas m ON f.fun_codigo = m.fun_codigo
        WHERE s.sol_codigo = :id LIMIT 1";
$stmt = $conexao->prepare($sql);
$stmt->bindParam(':id', $id_solicitacao, PDO::PARAM_INT);
$stmt->execute();

$solicitacao = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$solicitacao) {
    header("Location: index.php?error=not_found");
    exit;
}

$valor_formatado = number_format($solicitacao['sol_valor'], 2, ',', '.');
$status_class = strtolower($solicitacao['sol_status']) === 'aceita' ? 'status-accepted' : (strtolower($solicitacao['sol_status']) === 'recusada' ? 'status-rejected' : 'status-pending');
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitação #<?= $solicitacao['sol_codigo'] ?> | ZoomX</title>
    <link href="https://fonts.googleapis.com/css2?family=Righteous&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/solicitacao_pendente.css">
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Solicitação #<?= $solicitacao['sol_codigo'] ?></h1>
            <span class="status-badge <?= $status_class ?>">
                <?= ucfirst($solicitacao['sol_status']) ?>
            </span>
        </div>

        <div class="details-grid">
            <div class="detail-item">
                <span class="detail-label">Serviço</span>
                <span class="detail-value"><?= ucfirst($solicitacao['sol_servico']) ?></span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Valor</span>
                <span class="detail-value">R$ <?= $valor_formatado ?></span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Origem</span>
                <span class="detail-value"><?= htmlspecialchars($solicitacao['sol_origem']) ?></span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Destino</span>
                <span class="detail-value"><?= htmlspecialchars($solicitacao['sol_destino']) ?></span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Horário</span>
                <span class="detail-value"><?= date('H:i', strtotime($solicitacao['sol_data'])) ?></span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Distância</span>
                <span class="detail-value"><?= $solicitacao['sol_distancia'] ?> km</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Data</span>
                <span class="detail-value"><?= date('d/m/Y', strtotime($solicitacao['sol_data'])) ?></span>
            </div>
            <div class="detail-item" id="cancelContainer">
                <span class="detail-label">Cancelar solicitação <small>(<span id="countdown">10</span>s)</small></span>
                <form action="../actions/actionsolicitacao.php" method="POST" class="cancel-form">
                    <input type="hidden" name="acao" value="cancelar">
                    <input type="hidden" name="id" value="<?= $solicitacao['sol_codigo'] ?>">
                    <button type="submit" class="btn-cancel" id="cancelButton">Cancelar</button>
                </form>
            </div>

        </div>

        <?php if (!empty($solicitacao['sol_observacao'])): ?>
            <div class="detail-item">
                <span class="detail-label">Observações</span>
                <p><?= htmlspecialchars($solicitacao['sol_observacao']) ?></p>
            </div>
        <?php endif; ?>
        <div class="user-info">
            <h3>Informações do Cliente</h3>
            <div class="details-grid">
                <div class="detail-item">
                    <span class="detail-label">Nome</span>
                    <span class="detail-value"><?= htmlspecialchars($solicitacao['usu_nome']) ?></span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">E-mail</span>
                    <span class="detail-value"><?= htmlspecialchars($solicitacao['usu_email']) ?></span>
                </div>

                <?php if (!empty($solicitacao['usu_telefone'])): ?>
                    <div class="detail-item">
                        <span class="detail-label">Telefone</span>
                        <span class="detail-value"><?= htmlspecialchars($solicitacao['usu_telefone']) ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if (
            !empty($solicitacao['sol_lat_origem']) && !empty($solicitacao['sol_lng_origem']) &&
            !empty($solicitacao['sol_lat_destino']) && !empty($solicitacao['sol_lng_destino'])
        ): ?>
            <div class="map-container" id="map">
            </div>
        <?php endif; ?>
    </div>

    <div class="notification-popup" id="notificationPopup">
        <i class="fas fa-check-circle notification-icon notification-success" id="notificationIcon"></i>
        <div class="notification-content">
            <div class="notification-title" id="notificationTitle">Aguarde!</div>
            <div id="notificationMessage">Atualizando o status</div>
        </div>
    </div>

    <?php if (isset($_GET['status']) && in_array($_GET['status'], ['aceita', 'recusada'])): ?>
        <script>
            const popup = document.getElementById('notificationPopup');
            const icon = document.getElementById('notificationIcon');
            const title = document.getElementById('notificationTitle');
            const message = document.getElementById('notificationMessage');

            if ('<?= $_GET['status'] ?>' === 'aceita') {
                title.textContent = 'Atenção!';
                message.textContent = 'Aguarde no local indicado';
                icon.className = 'fas fa-check-circle notification-icon notification-success';
            } else {
                title.textContent = 'Aviso';
                message.textContent = 'Tente novamente mais tarde';
                icon.className = 'fas fa-times-circle notification-icon notification-error';
            }

            popup.classList.add('show');

            setTimeout(() => {
                popup.classList.remove('show');
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 300);
            }, 6000);
        </script>
    <?php endif; ?>

    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
    <script>
        <?php if (
            !empty($solicitacao['sol_lat_origem']) && !empty($solicitacao['sol_lng_origem']) &&
            !empty($solicitacao['sol_lat_destino']) && !empty($solicitacao['sol_lng_destino'])
        ): ?>

            function initMap() {
                const map = L.map('map').setView([-21.8732, -51.8432], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap'
                }).addTo(map);

                const origem = L.marker([
                        <?= $solicitacao['sol_lat_origem'] ?>,
                        <?= $solicitacao['sol_lng_origem'] ?>
                    ]).addTo(map)
                    .bindPopup("<b>Origem:</b><br><?= addslashes($solicitacao['sol_origem']) ?>");

                const destino = L.marker([
                        <?= $solicitacao['sol_lat_destino'] ?>,
                        <?= $solicitacao['sol_lng_destino'] ?>
                    ]).addTo(map)
                    .bindPopup("<b>Destino:</b><br><?= addslashes($solicitacao['sol_destino']) ?>");

                const bounds = L.latLngBounds([
                    [<?= $solicitacao['sol_lat_origem'] ?>, <?= $solicitacao['sol_lng_origem'] ?>],
                    [<?= $solicitacao['sol_lat_destino'] ?>, <?= $solicitacao['sol_lng_destino'] ?>]
                ]);
                map.fitBounds(bounds, {
                    padding: [50, 50]
                });
            }

            document.addEventListener('DOMContentLoaded', initMap);
        <?php endif; ?>

        let notificationShown = false;

        function showNotification(title, message, iconClass, redirect = false, id = null) {
            if (notificationShown) return;

            const popup = document.getElementById('notificationPopup');
            const icon = document.getElementById('notificationIcon');
            const titleEl = document.getElementById('notificationTitle');
            const messageEl = document.getElementById('notificationMessage');

            titleEl.textContent = title;
            messageEl.innerHTML = message;
            icon.className = iconClass;

            popup.classList.add('show');
            notificationShown = true;

            playNotificationSound();

            setTimeout(() => {
                popup.classList.remove('show');
                if (redirect && id) {
                    if (!window.location.search.includes('status=')) {
                        window.location.href = `solicitacao_pendente.php?id=${id}&status=${redirect}`;
                    }
                }
            }, 10000);
        }

        function playNotificationSound() {
            try {
                const audio = new Audio('../assets/notificacao.mp3');
                audio.play().catch(e => console.log('Autoplay prevented:', e));
            } catch (e) {
                console.log('Error playing sound:', e);
            }
        }

        function verificarStatus() {
            const id = <?= (int)$_GET['id'] ?>;
            fetch(`../api/verificar_notificacao.php?id=${id}&t=${new Date().getTime()}`) // Adiciona timestamp para evitar cache
                .then(res => res.json())
                .then(data => {
                    if (data.status !== 'pendente' && !notificationShown) {
                        if (data.status === 'aceita') {
                            const driverInfo = [
                                data.fun_nome ? `<strong>Mototaxista:</strong> ${data.fun_nome}` : '',
                                data.mot_modelo ? `<strong>Motocicleta:</strong> ${data.mot_modelo}` : '',
                                data.mot_placa ? `<strong>Placa:</strong> ${data.mot_placa}` : ''
                            ].filter(Boolean).join('<br>');

                            showNotification(
                                'Solicitação Aceita!',
                                `${data.mensagem}<br>${driverInfo}`,
                                'fas fa-check-circle notification-icon notification-success',
                                'aceita',
                                id
                            );
                        } else {
                            showNotification(
                                'Solicitação Recusada',
                                data.mensagem,
                                'fas fa-times-circle notification-icon notification-error',
                                'recusada',
                                id
                            );
                        }
                    }
                })
                .catch(console.error);
        }

        const horaAberturaServidor = <?= $hora_abertura ?>;
        const agora = Math.floor(Date.now() / 1000);
        const segundosDecorridos = agora - horaAberturaServidor;
        let tempoRestante = Math.max(0, 10 - segundosDecorridos);

        const countdownSpan = document.getElementById('countdown');
        const cancelButton = document.getElementById('cancelButton');
        const cancelContainer = document.getElementById('cancelContainer');

        function updateCancelButtonState() {
            if (tempoRestante <= 0) {
                if (cancelButton) {
                    cancelButton.disabled = true;
                    cancelButton.textContent = 'Tempo esgotado';
                }
                if (cancelContainer) {
                    cancelContainer.style.opacity = 0.5;
                    cancelContainer.style.pointerEvents = 'none';
                }
                return true;
            }
            return false;
        }

        if (updateCancelButtonState()) {} else {
            if (countdownSpan) countdownSpan.textContent = tempoRestante;

            const interval = setInterval(() => {
                tempoRestante--;
                if (countdownSpan) countdownSpan.textContent = tempoRestante;

                if (updateCancelButtonState()) {
                    clearInterval(interval);
                }
            }, 1000);
        }

        <?php if (!isset($_GET['status'])): ?>
            setInterval(verificarStatus, 5000);
            verificarStatus();
        <?php endif; ?>

        document.addEventListener('DOMContentLoaded', function() {
            const closeButton = document.getElementById('notificationClose');
            if (closeButton) {
                closeButton.addEventListener('click', function() {
                    document.getElementById('notificationPopup').classList.remove('show');
                });
            }
        });
    </script>
</body>

</html>
