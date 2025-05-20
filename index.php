<?php
session_start();

if (!isset($_SESSION["usuario"])) {
    $_SESSION["usuario"] = false;
    $_SESSION['tipo'] = 'visitante';
    $_SESSION['ativo'] = 0;
    $_SESSION["id"] = 0;
    $_SESSION["nome"] = 'Visitante';
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/bd/conexao.php';
$conexao = conexao::getInstance();

$sql = 'SELECT a.*, u.usu_nome FROM avaliacoes a JOIN usuarios u ON a.usu_codigo = u.usu_codigo ORDER BY a.ava_codigo DESC LIMIT 3';
$stmt = $conexao->prepare($sql);
$stmt->execute();
$avaliacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT * FROM anuncios ORDER BY anu_codigo DESC";
$stmt = $conexao->prepare($sql);
$stmt->execute();
$anuncios = $stmt->fetchAll(PDO::FETCH_ASSOC);

$grupos = array_chunk($anuncios, 4);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZoomX - Mototáxi Premium</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Righteous&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/homescreen.css">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-black fixed-top">
        <div class="container">
            <a class="navbar-brand font-righteous" href="#">ZOOMX</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Início</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#servicos">Serviços</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#como-funciona">Como Funciona</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#avaliacoes">Avaliações</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contato">Contato</a>
                    </li>
                    <li class="nav-item ms-lg-3 my-2 my-lg-0">
                        <a href="user/login.php" class="btn btn-accent">Entrar</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1 class="font-righteous">MOTOTÁXI PREMIUM</h1>
            <p class="lead mb-5">Viagens rápidas, seguras e com conforto. Agende seu mototáxi em poucos cliques.</p>
            <div class="d-flex flex-column flex-md-row justify-content-center gap-3">
                <a href="user/registrar_se.php" class="btn btn-accent btn-lg">Cadastre-se</a>
                <a href="#como-funciona" class="btn btn-outline-light btn-lg">Saiba Mais</a>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section id="servicos" class="py-5">
        <div class="container py-5">
            <h2 class="text-center section-title font-righteous">NOSSOS SERVIÇOS</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <h3>Entrega Rápida</h3>
                        <p>Entregamos documentos e pequenos pacotes com agilidade e segurança em toda a cidade.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="fas fa-taxi"></i>
                        </div>
                        <h3>Transporte Pessoal</h3>
                        <p>Leve você ao seu destino com rapidez, evitando trânsito e com total comodidade.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h3>Serviço 24h</h3>
                        <p>Atendimento ininterrupto para atender suas necessidades a qualquer momento.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How it Works -->
    <section id="como-funciona" class="py-5 bg-light">
        <div class="container py-5">
            <h2 class="text-center section-title font-righteous">COMO FUNCIONA</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-accent text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-weight: bold;">1</div>
                            <h3 class="ms-3 mb-0">Solicitação</h3>
                        </div>
                        <p>Faça seu cadastro e solicite seu mototáxi através do aplicativo ou site.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-accent text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-weight: bold;">2</div>
                            <h3 class="ms-3 mb-0">Confirmação</h3>
                        </div>
                        <p>Receba a confirmação com os detalhes do seu mototaxista em instantes.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-accent text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-weight: bold;">3</div>
                            <h3 class="ms-3 mb-0">Viagem</h3>
                        </div>
                        <p>Acompanhe em tempo real a rota do seu mototaxista até o local de embarque.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Parceiros -->
    <section class="py-5">
        <div class="container py-5">
            <h2 class="text-center section-title font-righteous">NOSSOS PARCEIROS</h2>
            <div class="row g-4">
                <?php foreach ($anuncios as $anuncio): ?>
                    <div class="col-6 col-md-3">
                        <div class="anuncio-card">
                            <?php if (!empty($anuncio['anu_foto'])): ?>
                                <img src="<?= htmlspecialchars($anuncio['anu_foto']) ?>" alt="Parceiro ZoomX" class="img-fluid">
                            <?php else: ?>
                                <img src="https://via.placeholder.com/300x200?text=Parceiro" alt="Parceiro ZoomX" class="img-fluid">
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section id="avaliacoes" class="py-5 bg-light">
        <div class="container py-5">
            <h2 class="text-center section-title font-righteous">AVALIAÇÕES</h2>
            <div class="row g-4">
                <?php if (!empty($avaliacoes)): ?>
                    <?php foreach ($avaliacoes as $avaliacao): ?>
                        <div class="col-md-4">
                            <div class="testimonial-card">
                                <div class="stars mb-3">
                                    <?php
                                    for ($i = 1; $i <= 5; $i++):
                                        $starClass = ($i <= $avaliacao['ava_nota']) ? 'fas fa-star text-warning' : 'far fa-star text-muted';
                                    ?>
                                        <i class="<?= $starClass ?>"></i>
                                    <?php endfor; ?>
                                </div>
                                <h5><?= htmlspecialchars($avaliacao['usu_nome']) ?></h5>
                                <p class="mb-4"><?= $avaliacao['ava_comentario'] ? htmlspecialchars($avaliacao['ava_comentario']) : "Ótimo serviço, recomendo!" ?></p>
                                <small class="text-muted"><?= date('d/m/Y', strtotime($avaliacao['ava_data_avaliacao'])) ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center">
                        <p>Nenhuma avaliação disponível no momento.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- App Download -->
    <section class="app-download py-5">
        <div class="container text-center py-4">
            <h2 class="font-righteous mb-4">BAIXE NOSSO APP</h2>
            <p class="lead mb-5">Disponível para Android e iOS. Agende suas corridas com apenas alguns toques.</p>
            <div class="d-flex flex-column flex-md-row justify-content-center gap-3">
                <a href="#" class="btn btn-accent btn-lg">
                    <i class="fab fa-google-play me-2"></i> Google Play
                </a>
                <a href="#" class="btn btn-light btn-lg">
                    <i class="fab fa-apple me-2"></i> App Store
                </a>
            </div>
        </div>
    </section>

    <!-- Contact -->
    <section id="contato" class="py-5">
        <div class="container py-5">
            <div class="row">
                <div class="col-lg-6 mb-5 mb-lg-0">
                    <h2 class="section-title font-righteous">CONTATO</h2>
                    <p class="lead mb-4">Entre em contato conosco para dúvidas ou parcerias.</p>
                    <form class="contact-form">
                        <div class="mb-3">
                            <input type="text" class="form-control" placeholder="Seu nome" required>
                        </div>
                        <div class="mb-3">
                            <input type="email" class="form-control" placeholder="Seu e-mail" required>
                        </div>
                        <div class="mb-3">
                            <textarea class="form-control" rows="5" placeholder="Sua mensagem" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-accent">Enviar Mensagem</button>
                    </form>
                </div>
                <div class="col-lg-6">
                    <div class="h-100">
                        <h3 class="h4 mb-4">Informações de Contato</h3>
                        <ul class="list-unstyled">
                            <li class="mb-3">
                                <i class="fas fa-map-marker-alt me-2 text-accent"></i> R. Paulo Sérgio Righetti, 45, Cidade Jardim, Presidente Venceslau - SP
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-phone me-2 text-accent"></i> (18) 1234-5678
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-envelope me-2 text-accent"></i> contato@zoomx.com.br
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-clock me-2 text-accent"></i> Atendimento 24/7
                            </li>
                        </ul>
                        <div class="mt-5">
                            <h3 class="h4 mb-4">Redes Sociais</h3>
                            <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="social-icon"><i class="fab fa-whatsapp"></i></a>
                            <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h3 class="h4 mb-4 font-righteous">ZOOMX</h3>
                    <p>Revolucionando o transporte urbano com tecnologia, agilidade e segurança.</p>
                </div>
                <div class="col-lg-2 col-md-4 mb-4">
                    <h3 class="h5 mb-4">Links</h3>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#">Início</a></li>
                        <li class="mb-2"><a href="#servicos">Serviços</a></li>
                        <li class="mb-2"><a href="#como-funciona">Como Funciona</a></li>
                        <li class="mb-2"><a href="#avaliacoes">Avaliações</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-4 mb-4">
                    <h3 class="h5 mb-4">Legal</h3>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#">Termos de Uso</a></li>
                        <li class="mb-2"><a href="#">Política de Privacidade</a></li>
                        <li class="mb-2"><a href="#">Cookies</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-4 mb-4">
                    <h3 class="h5 mb-4">Newsletter</h3>
                    <p>Assine para receber nossas novidades e promoções.</p>
                    <form class="d-flex">
                        <input type="email" class="form-control me-2" placeholder="Seu e-mail">
                        <button type="submit" class="btn btn-accent">Assinar</button>
                    </form>
                </div>
            </div>
            <hr class="my-4 bg-secondary">
            <div class="row">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    <p class="mb-0">&copy; 2025 ZoomX. Todos os direitos reservados.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.style.padding = '10px 0';
                navbar.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.1)';
            } else {
                navbar.style.padding = '15px 0';
                navbar.style.boxShadow = 'none';
            }
        });
        
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>

</html>