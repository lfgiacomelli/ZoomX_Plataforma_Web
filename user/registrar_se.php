<?php
session_start();

$erro_email = isset($_SESSION['erro_email']) ? $_SESSION['erro_email'] : '';
unset($_SESSION['erro_email']); 
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>ZoomX - Registrar</title>

  <!-- Fonte Righteous -->
  <link href="https://fonts.googleapis.com/css2?family=Righteous&display=swap" rel="stylesheet" />

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

  <!-- FontAwesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    body {
      font-family: 'Righteous', cursive;
      background-color: #f0f0f0;
      color: #000;
      min-height: 100vh;
      padding: 2rem;
    }
    label {
      font-weight: 600;
    }
    .form-control {
      border-radius: 0.375rem;
      border: 1px solid #ccc;
      padding: 0.5rem 1rem;
    }
    .password-container {
      position: relative;
    }
    .password-toggle {
      position: absolute;
      top: 50%;
      right: 1rem;
      transform: translateY(-50%);
      cursor: pointer;
      color: #666;
    }
    .btn {
      background-color: #000;
      color: #fff;
      font-weight: 700;
      border-radius: 0.375rem;
      padding: 0.5rem 1.5rem;
      transition: background-color 0.3s;
    }
    .btn:hover {
      background-color: #333;
    }
    .message-column {
      background: #fff;
      border-radius: 0.5rem;
      padding: 2rem;
      box-shadow: 0 0 15px rgb(0 0 0 / 0.1);
      height: 100%;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }
    .message-column h2 {
      margin-bottom: 1rem;
    }
    .message-column p {
      font-size: 1.1rem;
      line-height: 1.5;
    }
    @media (max-width: 767.98px) {
      .message-column {
        margin-top: 2rem;
        height: auto;
      }
    }
  </style>
</head>

<body>
  <div class="container-fluid">
    <div class="row justify-content-center align-items-center" style="min-height: 80vh;">
      <!-- Coluna do Form -->
      <div class="col-12 col-md-5 col-lg-4">
        <h1 class="mb-4">ZoomX</h1>
        <p class="mb-4">Crie sua conta gratuita</p>
        <form action="../actions/actionregistrar_se.php" method="POST" novalidate>
          <div class="mb-3">
            <label for="nome" class="form-label">Nome Completo</label>
            <input type="text" name="nome" id="nome" class="form-control" required placeholder="Digite seu nome" value="<?php echo isset($_SESSION['form_data']['nome']) ? htmlspecialchars($_SESSION['form_data']['nome']) : ''; ?>">
          </div>

          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-control" required placeholder="Digite seu email" value="<?php echo isset($_SESSION['form_data']['email']) ? htmlspecialchars($_SESSION['form_data']['email']) : ''; ?>">
          </div>

          <div class="mb-3 password-container">
            <label for="senha" class="form-label">Senha</label>
            <input type="password" name="senha" id="senha" class="form-control" placeholder="Insira sua Senha" autocomplete="new-password" required>
            <i class="fas fa-eye password-toggle" id="togglePassword"></i>
          </div>

          <div class="mb-3">
            <label for="telefone" class="form-label">Telefone</label>
            <input type="text" name="telefone" id="telefone" class="form-control" required placeholder="(00) 00000-0000" value="<?php echo isset($_SESSION['form_data']['telefone']) ? htmlspecialchars($_SESSION['form_data']['telefone']) : ''; ?>">
          </div>

          <input type="hidden" name="acao" value="adicionar" />
          <input type="hidden" name="ativo" value="true" />
          <input type="hidden" name="created_at" value="<?php echo date('Y-m-d H:i:s'); ?>" />
          <input type="hidden" name="updated_at" value="<?php echo date('Y-m-d H:i:s'); ?>" />

          <button type="submit" class="btn w-100">Registrar-se</button>
        </form>
        <p class="mt-3">
          Já tem uma conta? <a href="login.php">Faça login</a>
        </p>
      </div>

      <div class="col-12 col-md-5 col-lg-4 message-column">
        <h2>Bem-vindo ao ZoomX!</h2>
        <p>
          Aqui você pode se registrar para aproveitar nossos serviços de moto táxi e moto entregas com rapidez e segurança. Cadastre-se e comece agora mesmo!
        </p>
        <p>
          Caso tenha dúvidas, entre em contato com nosso suporte.
        </p>
      </div>
    </div>
  </div>

  <script>
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#senha');

    togglePassword.addEventListener('click', function () {
      const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
      password.setAttribute('type', type);
      this.classList.toggle('fa-eye-slash');
    });

    document.getElementById('telefone').addEventListener('input', function (e) {
      let value = e.target.value.replace(/\D/g, '');
      if (value.length > 11) value = value.substring(0, 11);

      if (value.length > 2) {
        value = `(${value.substring(0, 2)}) ${value.substring(2)}`;
      }
      if (value.length > 10) {
        value = `${value.substring(0, 10)}-${value.substring(10)}`;
      }

      e.target.value = value;
    });

    <?php if (!empty($erro_email)): ?>
      Swal.fire({
        title: 'E-mail já cadastrado',
        html: `<?php echo $erro_email; ?><br><br>
                <a href="login.php" style="color: #721c24; font-weight: bold; text-decoration: underline;">
                    Clique aqui para fazer login
                </a>`,
        icon: 'error',
        confirmButtonColor: '#000',
        confirmButtonText: 'Entendi',
        allowOutsideClick: false,
        customClass: {
          confirmButton: 'righteous-font'
        }
      });
    <?php endif; ?>
  </script>

</body>

</html>

<?php
if (isset($_SESSION['form_data'])) {
  unset($_SESSION['form_data']);
}
?>
