<?php
include '../components/verifica_sessao.php';

require '../bd/conexao.php';
$conexao = conexao::getInstance();

$sql = "SELECT v.via_codigo, v.via_data, v.via_status
FROM viagens v
LEFT JOIN avaliacoes a ON a.via_codigo = v.via_codigo
WHERE v.usu_codigo = :usu_codigo
  AND v.via_status = 'finalizada'
  AND a.via_codigo IS NULL
ORDER BY v.via_data DESC
LIMIT 1";
$stmt = $conexao->prepare($sql);
$stmt->bindValue(':usu_codigo', $_SESSION['id'], PDO::PARAM_INT);
$stmt->execute();
$ultimaViagemSemAvaliacao = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZoomX - Tela Inicial</title>
    <link href="https://fonts.googleapis.com/css2?family=Righteous&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/index.css">
    <style>
        .floating-notification {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #222;
            color: #fff;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            z-index: 9999;
            animation: slideIn 0.5s ease-out;
            font-family: Arial, sans-serif;
        }

        .floating-notification a {
            color: #4fc3f7;
            text-decoration: underline;
        }

        @keyframes slideIn {
            from {
                transform: translateY(30px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>
</head>

<body>
    <?php include '../components/header.php'; ?>

    <section class="hero">
        <div class="container">
            <h1 class="fade-in">Seu transporte rápido e seguro</h1>
            <p class="fade-in delay-1">MotoTáxi e MotoEntrega com a qualidade ZoomX</p>
            <a href="solicitar_corrida.php" class="btn btn-hero fade-in delay-2">Solicitar Agora <i class="bi bi-arrow-right"></i></a>
        </div>
    </section>
    <?php if ($ultimaViagemSemAvaliacao): ?>
        <div class="floating-notification">
            <p>
                Você tem uma viagem pendente para avaliação.
                <a href="avaliar_viagem.php?via_codigo=<?= $ultimaViagemSemAvaliacao['via_codigo'] ?>">Avaliar agora</a>
            </p>
        </div>
    <?php endif; ?>
    <div class="container main-content">
        <div class="actions-grid">
            <div class="action-card fade-in">
                <img src="../assets/motorcycle.png" alt="MotoTáxi" style="width: 100px; height: 100px;">
                <h3>MotoTáxi</h3>
                <p>Solicite uma corrida com segurança e conforto para qualquer destino em Presidente Venceslau</p>
                <a href="solicitar_corrida.php">Pedir Agora</a>
            </div>

            <div class="action-card fade-in delay-1">
                <img src="../assets/box.png" alt="MotoEntrega" style="width: 100px; height: 100px;">
                <h3>MotoEntrega</h3>
                <p>Envie e receba pacotes com rapidez e segurança. Deixe suas entregas por nossa conta e ganhe mais eficiência.</p>
                <a href="solicitar_entrega.php">Solicitar Entrega</a>
            </div>

            <div class="action-card fade-in delay-2">
                <img src="../assets/historico.png" alt="Histórico" style="width: 100px; height: 100px;">
                <h3>Histórico</h3>
                <p>Confira o histórico completo das suas solicitações e avaliações anteriores com facilidade.</p>
                <a href="historico.php">Ver Histórico</a>
            </div>

            <div class="action-card fade-in delay-3">
                <img src="../assets/conta.png" alt="Perfil" style="width: 100px; height: 100px;">
                <h3>Perfil</h3>
                <p>Administre suas informações pessoais e ajuste suas preferências de serviço de forma prática.</p>
                <a href="conta.php">Acessar Perfil</a>
            </div>
        </div>
    </div>
    <?php include '../components/anuncios.php'; ?>
    <section class="features">
        <div class="container">
            <div class="row">
                <div class="col-md-4 feature-item">
                    <i class="bi bi-lightning-charge"></i>
                    <h3>Rapidez</h3>
                    <p>Tempo médio de espera de apenas 5 minutos em qualquer região</p>
                </div>
                <div class="col-md-4 feature-item">
                    <i class="bi bi-shield-check"></i>
                    <h3>Segurança</h3>
                    <p>Motoristas verificados e treinados para sua tranquilidade</p>
                </div>
                <div class="col-md-4 feature-item">
                    <i class="bi bi-currency-dollar"></i>
                    <h3>Preço Justo</h3>
                    <p>Tarifas competitivas e sem cobranças extras</p>
                </div>
            </div>
        </div>
    </section>
    <?php include '../components/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const animateElements = document.querySelectorAll('.fade-in');

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = 1;
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, {
                threshold: 0.1
            });

            animateElements.forEach(el => {
                el.style.opacity = 0;
                el.style.transform = 'translateY(20px)';
                el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                observer.observe(el);
            });
        });
    </script>
</body>

</html>
