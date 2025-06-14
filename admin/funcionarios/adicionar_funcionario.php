<?php
include '../../components/verifica_sessao_admin.php';


require '../../bd/conexao.php';
$conexao = conexao::getInstance();

$sql = "SELECT * FROM anuncios";
$stmt = $conexao->prepare($sql);
$stmt->execute();
$anuncios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Registrar Funcionário</title>
    <link href="https://fonts.googleapis.com/css2?family=Righteous&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #000;
            --background: #f0f0f0;
            --white: #fff;
            --button-color: #28a745;
            --button-hover: #218838;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Righteous', sans-serif;
            background-color: var(--background);
            color: var(--primary);
        }

        h1 {
            text-align: center;
            font-size: 2rem;
            margin: 40px 0 20px;
        }

        .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 60px;
            padding: 20px;
        }

        form {
            width: 100%;
            max-width: 400px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 6px;
            font-size: 1rem;
        }

        input,
        select {
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #ccc;
            background-color: #fafafa;
            font-size: 1rem;
            font-family: 'Righteous', sans-serif;
        }

        input:focus,
        select:focus {
            border-color: #007bff;
            background-color: #fff;
            outline: none;
        }

        .hidden-field {
            display: none;
        }

        button {
            background-color: var(--button-color);
            color: var(--white);
            border: none;
            padding: 14px;
            font-size: 1.1rem;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: var(--button-hover);
        }

        .anuncio {
            width: 400px;
            height: 300px;
            border-radius: 12px;
            overflow: hidden;
            position: relative;
        }

        .anuncio img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 12px;
            position: absolute;
            top: 0;
            left: 0;
            display: none;
        }

        .anuncio img.active {
            display: block;
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
                align-items: center;
            }

            .anuncio {
                width: 100%;
                max-width: 400px;
                height: auto;
                aspect-ratio: 4 / 3;
            }
        }
    </style>
</head>
<body>
    <?php include '../../components/header_admin.php'; ?>

    <h1>Registrar Novo Funcionário</h1>

    <div class="container">
        <form action="../../actions/actionfuncionario.php" method="POST">
            <div class="form-group">
                <label for="nome">Nome Completo:</label>
                <input type="text" name="nome" id="nome" required />
            </div>

            <div class="form-group">
                <label for="email">E-mail:</label>
                <input type="email" name="email" id="email" required />
            </div>

            <div class="form-group">
                <label for="senha">Senha:</label>
                <input type="password" name="senha" id="senha" required />
            </div>

            <div class="form-group">
                <label for="telefone">Telefone:</label>
                <input type="text" name="telefone" id="telefone" maxlength="15" required />
            </div>
            <div class="form-group">
                <label for="cpf">CPF do Funcionário:</label>
                <input type="text" name="cpf" id="cpf" maxlength="11" required />
            </div>

            <div class="form-group">
                <label for="cargo">Cargo:</label>
                <select name="cargo" id="cargo" onchange="toggleCNHField()" required>
                    <option value="atendente">Atendente</option>
                    <option value="mototaxista">Mototaxista</option>
                </select>
            </div>

            <div class="form-group hidden-field" id="cnh-field">
                <label for="cnh">Número da CNH:</label>
                <input type="text" name="cnh" id="cnh" maxlength="11" />
            </div>

            <input type="hidden" name="acao" value="adicionar" />
            <input type="hidden" name="ativo" value="true" />
            <input type="hidden" name="data_contratacao" value="<?= date('Y-m-d'); ?>" />

            <button type="submit">Registrar Funcionário</button>
        </form>

        <div class="anuncio" id="anuncio-box">
            <?php foreach ($anuncios as $index => $anuncio): ?>
                <img src="<?= $anuncio['anu_foto'] ?>" class="<?= $index === 0 ? 'active' : '' ?>" />
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        function toggleCNHField() {
            const cargo = document.getElementById("cargo").value;
            const cnhField = document.getElementById("cnh-field");
            const cnhInput = document.getElementById("cnh");

            if (cargo === "mototaxista") {
                cnhField.classList.remove("hidden-field");
                cnhInput.setAttribute("required", "required");
            } else {
                cnhField.classList.add("hidden-field");
                cnhInput.removeAttribute("required");
            }
        }

        document.getElementById("telefone").addEventListener("input", function () {
            let v = this.value.replace(/\D/g, '');
            v = v.replace(/^(\d{2})(\d)/g, '($1) $2');
            v = v.replace(/(\d)(\d{4})$/, '$1-$2');
            this.value = v;
        });

        window.onload = () => {
            toggleCNHField();

            const imagens = document.querySelectorAll('.anuncio img');
            let index = 0;

            setInterval(() => {
                imagens[index].classList.remove('active');
                index = (index + 1) % imagens.length;
                imagens[index].classList.add('active');
            }, 10000);
        };
        document.getElementById("cpf").addEventListener("input", function () {
            let v = this.value.replace(/\D/g, '');
            v = v.replace(/(\d{3})(\d)/, '$1.$2');
            v = v.replace(/(\d{3})(\d{2})$/, '$1-$2');
            this.value = v;
        });
    </script>
</body>
</html>
