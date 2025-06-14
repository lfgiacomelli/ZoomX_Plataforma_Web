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
    <title>ZoomX - Transporte Rápido e Seguro de Mototáxi</title>
    <meta name="description" content="ZoomX oferece serviço de mototáxi rápido, seguro e acessível. Conectamos você aos melhores mototaxistas da cidade.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Righteous&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/homescreen.css">
</head>

<body data-bs-spy="scroll" data-bs-target=".navbar" data-bs-offset="100">
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand brand-font fs-3" href="#">ZOOM<span class="text-gradient">X</span></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#home">Início</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Vantagens</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#how-it-works">Como Funciona</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#partners">Parceiros</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#testimonials">Avaliações</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contato</a>
                    </li>
                    <li class="nav-item ms-lg-3 my-2 my-lg-0">
                        <a href="user/login.php" class="btn btn-zoomx">Entrar</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-section">
        <div class="container position-relative z-index-1">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold text-white mb-4">Transporte rápido e seguro na palma da sua mão</h1>
                    <p class="lead text-white mb-5">O ZoomX conecta você aos melhores mototaxistas da cidade com apenas alguns toques. Chegue ao seu destino mais rápido, com segurança e conforto.</p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="user/registrar_se.php" class="btn btn-zoomx btn-lg px-4 py-3">Cadastre-se Grátis</a>
                        <a href="#how-it-works" class="btn btn-outline-light btn-lg px-4 py-3">Saiba Mais</a>
                    </div>
                </div>
                <div class="col-lg-6 d-none d-lg-block">
                    <img src="https://via.placeholder.com/600x600?text=App+ZoomX" alt="Aplicativo ZoomX" class="img-fluid app-screenshot">
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-6 bg-light">
        <div class="container">
            <div class="text-center mb-6">
                <span class="badge bg-primary bg-opacity-10 text-primary mb-3">POR QUE ESCOLHER O ZOOMX</span>
                <h2 class="display-5 fw-bold mb-4">Vantagens do nosso serviço</h2>
                <p class="lead text-muted mx-auto" style="max-width: 700px;">Oferecemos a melhor experiência em transporte rápido com mototáxi, combinando tecnologia, segurança e confiabilidade</p>
            </div>
            
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <h3 class="h4 text-center mb-3">Rápido</h3>
                        <p class="text-center text-muted mb-0">Tempo médio de espera de apenas 5 minutos em qualquer ponto da cidade.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3 class="h4 text-center mb-3">Seguro</h3>
                        <p class="text-center text-muted mb-0">Todos os mototaxistas são verificados e treinados regularmente.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <h3 class="h4 text-center mb-3">Acessível</h3>
                        <p class="text-center text-muted mb-0">Preços até 40% mais baratos que táxis tradicionais.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-headset"></i>
                        </div>
                        <h3 class="h4 text-center mb-3">Suporte 24/7</h3>
                        <p class="text-center text-muted mb-0">Atendimento ao cliente disponível a qualquer momento.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-5 bg-gradient-primary text-white">
        <div class="container">
            <div class="row">
                <div class="col-6 col-md-3">
                    <div class="stats-item">
                        <div class="stats-number">15K+</div>
                        <div class="stats-label">Clientes Satisfeitos</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stats-item">
                        <div class="stats-number">500+</div>
                        <div class="stats-label">Mototaxistas Parceiros</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stats-item">
                        <div class="stats-number">98%</div>
                        <div class="stats-label">Avaliações Positivas</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stats-item">
                        <div class="stats-number">24/7</div>
                        <div class="stats-label">Disponibilidade</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="py-6">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <span class="badge bg-primary bg-opacity-10 text-primary mb-3">FUNCIONAMENTO</span>
                    <h2 class="display-5 fw-bold mb-4">Como o ZoomX funciona</h2>
                    <p class="lead text-muted mb-5">Em apenas 3 passos simples você estará no seu destino</p>
                </div>
            </div>
            
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="how-it-works-step">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3 class="h4 mb-3">Baixe o aplicativo</h3>
                            <p class="text-muted mb-0">Disponível para iOS e Android. Faça seu cadastro em menos de 2 minutos e tenha acesso imediato ao serviço.</p>
                        </div>
                    </div>
                    
                    <div class="how-it-works-step">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3 class="h4 mb-3">Solicite sua viagem</h3>
                            <p class="text-muted mb-0">Informe seu local de partida e destino. Nosso sistema encontrará o mototaxista mais próximo disponível.</p>
                        </div>
                    </div>
                    
                    <div class="how-it-works-step">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h3 class="h4 mb-3">Acompanhe e viaje</h3>
                            <p class="text-muted mb-0">Acompanhe em tempo real a rota do seu mototaxista. Receba notificações quando ele estiver chegando.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-5">
                <a href="#download" class="btn btn-zoomx btn-lg px-5">Baixar Aplicativo</a>
            </div>
        </div>
    </section>

    <!-- Partners Section -->
    <?php if(!empty($anuncios)): ?>
    <section id="partners" class="py-6 bg-light">
        <div class="container">
            <div class="text-center mb-6">
                <span class="badge bg-primary bg-opacity-10 text-primary mb-3">PARCEIROS</span>
                <h2 class="display-5 fw-bold mb-4">Empresas que confiam no ZoomX</h2>
                <p class="lead text-muted mx-auto" style="max-width: 700px;">Colaboramos com as melhores empresas para oferecer benefícios exclusivos aos nossos clientes</p>
            </div>
            
            <div class="row g-4">
                <?php foreach($anuncios as $anuncio): ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="partner-logo">
                        <?php if(!empty($anuncio['anu_foto'])): ?>
                            <img src="<?= htmlspecialchars($anuncio['anu_foto']) ?>" alt="<?= htmlspecialchars($anuncio['anu_titulo']) ?>">
                        <?php else: ?>
                            <img src="https://via.placeholder.com/150x80?text=Logo" alt="Parceiro">
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Testimonials Section -->
    <section id="testimonials" class="py-6">
        <div class="container">
            <div class="text-center mb-6">
                <span class="badge bg-primary bg-opacity-10 text-primary mb-3">DEPOIMENTOS</span>
                <h2 class="display-5 fw-bold mb-4">O que nossos clientes dizem</h2>
                <p class="lead text-muted mx-auto" style="max-width: 700px;">Avaliações reais de pessoas que já experimentaram o serviço ZoomX</p>
            </div>
            
            <div class="row g-4">
                <?php if(!empty($avaliacoes)): ?>
                    <?php foreach($avaliacoes as $avaliacao): ?>
                    <div class="col-md-4">
                        <div class="testimonial-card">
                            <div class="mb-3">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <?php if($i <= $avaliacao['ava_nota']): ?>
                                        <i class="fas fa-star text-warning"></i>
                                    <?php else: ?>
                                        <i class="fas fa-star text-muted"></i>
                                    <?php endif; ?>
                                <?php endfor; ?>
                                <h6 class="mb-0 mt-2"><?= htmlspecialchars($avaliacao['usu_nome']) ?></h6>
                            </div>
                            <p class="mb-4">
                                <?= $avaliacao['ava_comentario'] ? htmlspecialchars($avaliacao['ava_comentario']) : "Ótimo serviço, recomendo!" ?>
                            </p>
                            <div class="d-flex align-items-center">
                                <div>
                                    <small class="text-muted">
                                        <?= date('d/m/Y', strtotime($avaliacao['ava_data_avaliacao'])) ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center">
                        <p class="text-muted">Nenhuma avaliação disponível no momento.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Download Section -->
    <section id="download" class="py-6 bg-dark text-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-5 mb-lg-0">
                    <h2 class="display-5 fw-bold mb-4">Baixe o aplicativo ZoomX agora</h2>
                    <p class="lead mb-5">Disponível para iOS e Android. Comece a usar hoje mesmo e experimente a melhor forma de se locomover pela cidade.</p>
                    
                    <div class="d-flex flex-wrap gap-3">
                        <a href="#" class="btn btn-light btn-lg px-4">
                            <i class="fab fa-apple fa-2x me-2"></i>
                            <div class="d-flex flex-column text-start">
                                <small>Disponível na</small>
                                <strong>App Store</strong>
                            </div>
                        </a>
                        <a href="#" class="btn btn-light btn-lg px-4">
                            <i class="fab fa-google-play fa-2x me-2"></i>
                            <div class="d-flex flex-column text-start">
                                <small>Disponível no</small>
                                <strong>Google Play</strong>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <img src="https://via.placeholder.com/600x600?text=Mobile+Screens" alt="Aplicativo Mobile" class="img-fluid" style="max-height: 400px;">
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-6">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center mb-6">
                    <span class="badge bg-primary bg-opacity-10 text-primary mb-3">CONTATO</span>
                    <h2 class="display-5 fw-bold mb-4">Fale conosco</h2>
                    <p class="lead text-muted mx-auto" style="max-width: 700px;">Tem dúvidas, sugestões ou precisa de ajuda? Nossa equipe está pronta para te atender.</p>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-6 mb-5 mb-lg-0">
                    <form class="needs-validation" novalidate>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Seu nome</label>
                                <input type="text" class="form-control" id="name" required>
                                <div class="invalid-feedback">
                                    Por favor, informe seu nome.
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Seu e-mail</label>
                                <input type="email" class="form-control" id="email" required>
                                <div class="invalid-feedback">
                                    Por favor, informe um e-mail válido.
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="subject" class="form-label">Assunto</label>
                                <input type="text" class="form-control" id="subject" required>
                                <div class="invalid-feedback">
                                    Por favor, informe o assunto.
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="message" class="form-label">Mensagem</label>
                                <textarea class="form-control" id="message" rows="4" required></textarea>
                                <div class="invalid-feedback">
                                    Por favor, escreva sua mensagem.
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-zoomx px-4 py-3">Enviar Mensagem</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-lg-6">
                    <div class="contact-info-item">
                        <div class="contact-info-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="contact-info-content">
                            <h4 class="h5 mb-2">Endereço</h4>
                            <p class="mb-0">R. Paulo Sérgio Righetti, 45, Cidade Jardim, Presidente Venceslau - SP</p>
                        </div>
                    </div>
                    
                    <div class="contact-info-item">
                        <div class="contact-info-icon">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div class="contact-info-content">
                            <h4 class="h5 mb-2">Telefone</h4>
                            <p class="mb-0">(18) 99999-9999</p>
                        </div>
                    </div>
                    
                    <div class="contact-info-item">
                        <div class="contact-info-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contact-info-content">
                            <h4 class="h5 mb-2">E-mail</h4>
                            <p class="mb-0">contato@zoomx.com.br</p>
                        </div>
                    </div>
                    
                    <div class="contact-info-item">
                        <div class="contact-info-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="contact-info-content">
                            <h4 class="h5 mb-2">Horário de Atendimento</h4>
                            <p class="mb-0">Segunda a Sexta: 8h às 18h<br>Sábado: 8h às 12h</p>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <h4 class="h5 mb-3">Redes Sociais</h4>
                        <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-4">
                    <h3 class="brand-font fs-3 text-white mb-4">ZOOM<span class="text-gradient">X</span></h3>
                    <p class="text-muted">Revolucionando o transporte urbano com agilidade, segurança e tecnologia.</p>
                    <div class="mt-4">
                        <a href="#" class="social-icon me-2"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-icon me-2"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-icon me-2"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="footer-links">
                        <h3 class="h5 text-white mb-4">Links</h3>
                        <ul class="list-unstyled">
                            <li class="mb-2"><a href="#home">Início</a></li>
                            <li class="mb-2"><a href="#features">Vantagens</a></li>
                            <li class="mb-2"><a href="#how-it-works">Como Funciona</a></li>
                            <li class="mb-2"><a href="#testimonials">Avaliações</a></li>
                            <li class="mb-2"><a href="#contact">Contato</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="footer-links">
                        <h3 class="h5 text-white mb-4">Legal</h3>
                        <ul class="list-unstyled">
                            <li class="mb-2"><a href="#">Termos de Uso</a></li>
                            <li class="mb-2"><a href="#">Política de Privacidade</a></li>
                            <li class="mb-2"><a href="#">Cookies</a></li>
                            <li class="mb-2"><a href="#">FAQ</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-md-4 col-lg-4">
                    <div class="footer-links">
                        <h3 class="h5 text-white mb-4">Newsletter</h3>
                        <p class="text-muted">Assine nossa newsletter para receber novidades e promoções exclusivas.</p>
                        <form class="mt-4">
                            <div class="input-group mb-3">
                                <input type="email" class="form-control" placeholder="Seu e-mail" required>
                                <button class="btn btn-zoomx" type="submit">Assinar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <hr class="my-4 border-secondary">
            
            <div class="row">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0 text-muted">&copy; 2025 ZoomX. Todos os direitos reservados.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <p class="mb-0 text-muted">Desenvolvido com <i class="fas fa-heart text-danger"></i> pela equipe ZoomX</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
        
        // Form validation
        (function() {
            'use strict';
            
            var forms = document.querySelectorAll('.needs-validation');
            
            Array.prototype.slice.call(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    
                    form.classList.add('was-validated');
                }, false);
            });
        })();
        
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 80,
                        behavior: 'smooth'
                    });
                }
            });
        });
    </script>
</body>

</html>