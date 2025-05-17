<?php
require '../bd/conexao.php';
$conexao = conexao::getInstance();

if (!isset($_GET['id'])) {
    die("ID não fornecido.");
}

$id = intval($_GET['id']);
$sql = 'SELECT v.*, u.usu_nome, v.fun_codigo, f.fun_nome, s.sol_data, s.sol_distancia
FROM viagens v
INNER JOIN usuarios u ON v.usu_codigo = u.usu_codigo
INNER JOIN solicitacoes s ON v.sol_codigo = s.sol_codigo
INNER JOIN funcionarios f ON v.fun_codigo = f.fun_codigo
WHERE v.via_codigo = :id;';
$stmt = $conexao->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$dados = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$dados) {
    die("Recibo não encontrado.");
}

// Formatar data e valor
$dataFormatada = date('d/m/Y H:i', strtotime($dados['sol_data']));
$valorFormatado = number_format($dados['via_valor'], 2, ',', '.');
$tipoServico = ($dados['via_servico'] == 'mototaxi') ? 'Corrida' : 'Entrega';
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Recibo ZoomX - <?= $tipoServico ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Righteous&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        :root {
            --primary: #000;
            --secondary: #f0f0f0;
            --accent: #000;
            --light: #f0f0f0;
            --dark: #000;
            --success: #f0f0f0;
        }
        
        body {
            font-family: 'Open Sans', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8eb 100%);
            padding: 40px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .recibo-container {
            width: 100%;
            max-width: 680px;
            margin: 0 auto;
            perspective: 1000px;
        }

        .recibo {
            width: 100%;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(0, 0, 0, 0.05);
            transform-style: preserve-3d;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .recibo::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 8px;
            background: linear-gradient(90deg, var(--secondary), var(--accent));
        }

        .logo-container {
            text-align: center;
            margin-bottom: 25px;
            position: relative;
        }

        .logo {
            height: 60px;
            margin-bottom: 10px;
        }

        .recibo-title {
            font-family: 'Righteous', sans-serif;
            text-align: center;
            margin: 20px 0 30px;
            color: var(--primary);
            font-size: 28px;
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .recibo-title::after {
            content: '';
            display: block;
            width: 80px;
            height: 3px;
            background: var(--secondary);
            margin: 10px auto 0;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .info-item {
            margin-bottom: 15px;
        }

        .info-label {
            font-family: 'Righteous', sans-serif;
            font-size: 14px;
            color: var(--primary);
            margin-bottom: 5px;
            letter-spacing: 0.5px;
        }

        .info-value {
            font-size: 16px;
            font-weight: 600;
            color: var(--dark);
            padding: 8px 12px;
            background: var(--light);
            border-radius: 6px;
            border-left: 3px solid var(--accent);
        }

        .valor-total {
            grid-column: span 2;
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px dashed #ddd;
        }

        .valor-total .info-label {
            font-size: 16px;
        }

        .valor-total .info-value {
            font-size: 24px;
            color: var(--secondary);
            background: none;
            padding: 10px 0;
            border: none;
        }

        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 14px;
            color: #7F8C8D;
            position: relative;
            padding-top: 20px;
        }

        .footer::before {
            content: '';
            display: block;
            width: 100px;
            height: 1px;
            background: linear-gradient(90deg, transparent, #ddd, transparent);
            margin: 0 auto 15px;
        }

        .codigo-recibo {
            position: absolute;
            top: 20px;
            right: 20px;
            font-family: 'Righteous', sans-serif;
            font-size: 14px;
            color: #95A5A6;
        }

        #btnPDF {
            display: block;
            margin: 30px auto 0;
            padding: 12px 30px;
            font-family: 'Righteous', sans-serif;
            font-size: 16px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        #btnPDF:hover {
            background: var(--dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        @media print {
            body {
                background: none;
                padding: 0;
            }
            
            #btnPDF {
                display: none;
            }
            
            .recibo {
                box-shadow: none;
                border: none;
                padding: 30px;
            }
        }
    </style>
</head>

<body>

    <div class="recibo-container">
        <div class="recibo" id="recibo">
            <span class="codigo-recibo">#<?= str_pad($id, 6, '0', STR_PAD_LEFT) ?></span>
            
            <div class="logo-container">
                <img src="../assets/logo.png" class="logo" alt="Logo ZoomX">
                <h1 class="recibo-title">Recibo de <?= $tipoServico ?></h1>
            </div>

            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Cliente</div>
                    <div class="info-value"><?= htmlspecialchars($dados['usu_nome']) ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Motorista</div>
                    <div class="info-value"><?= htmlspecialchars($dados['fun_nome']) ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Origem</div>
                    <div class="info-value"><?= htmlspecialchars($dados['via_origem']) ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Destino</div>
                    <div class="info-value"><?= htmlspecialchars($dados['via_destino']) ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Tipo de Serviço</div>
                    <div class="info-value"><?= ucfirst($dados['via_servico']) ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Data e Hora</div>
                    <div class="info-value"><?= $dataFormatada ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Distância</div>
                    <div class="info-value"><?= $dados['sol_distancia'] ?> km</div>
                </div>
                
                <div class="valor-total">
                    <div class="info-label">Valor Total</div>
                    <div class="info-value">R$ <?= $valorFormatado ?></div>
                </div>
            </div>

            <div class="footer">
                Obrigado por utilizar os serviços ZoomX!<br>
                Em caso de dúvidas, entre em contato com nosso suporte.
            </div>
        </div>
    </div>

    <button id="btnPDF">Gerar PDF</button>

    <script>
        document.getElementById('btnPDF').addEventListener('click', async () => {
            const { jsPDF } = window.jspdf;
            const recibo = document.getElementById("recibo");
            
            // Adicionar efeito visual durante a geração do PDF
            recibo.style.transform = 'rotateY(10deg)';
            
            const canvas = await html2canvas(recibo, {
                scale: 2,
                logging: false,
                useCORS: true,
                allowTaint: true
            });
            
            // Resetar transformação
            recibo.style.transform = 'none';
            
            const imgData = canvas.toDataURL("image/png", 1.0);
            const pdf = new jsPDF('p', 'mm', 'a4');
            const pdfWidth = pdf.internal.pageSize.getWidth();
            const pdfHeight = pdf.internal.pageSize.getHeight();
            
            // Calcular proporções para centralizar verticalmente
            const imgProps = pdf.getImageProperties(imgData);
            const imgRatio = imgProps.width / imgProps.height;
            const pdfRatio = pdfWidth / pdfHeight;
            
            let imgWidth = pdfWidth - 20; // Margem lateral de 10mm
            let imgHeight = imgWidth / imgRatio;
            
            // Se a altura for maior que a página, ajustar pela altura
            if (imgHeight > pdfHeight - 20) {
                imgHeight = pdfHeight - 20;
                imgWidth = imgHeight * imgRatio;
            }
            
            const x = (pdfWidth - imgWidth) / 2;
            const y = (pdfHeight - imgHeight) / 2;
            
            pdf.addImage(imgData, 'PNG', x, y, imgWidth, imgHeight);
            
            pdf.setProperties({
                title: `Recibo ZoomX - ${document.title}`,
                subject: 'Recibo de serviço',
                author: 'ZoomX',
                keywords: 'recibo, zoomx, transporte',
                creator: 'ZoomX'
            });
            
            pdf.save(`recibo_zoomx_${<?= $id ?>}.pdf`);
        });
    </script>

</body>

</html>