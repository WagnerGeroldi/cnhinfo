<?php
require_once __DIR__ . '/config/functions.php';
$pageTitle = 'Resultado da consulta';
$assetBase = '.';

$nome = normalizeText($_POST['nome_completo'] ?? '');
$matricula = normalizeText($_POST['matricula'] ?? '');
$registro = null;
$erro = null;
$bloquearVisualizacao = false;
$whatsContatoUrl = '';

if ($nome === '' || $matricula === '') {
    $erro = 'Informe nome completo e matrícula para realizar a consulta.';
} else {
    $pdo = getPDO();

    $stmt = $pdo->prepare("
        SELECT 
            a.*,
            c.nome_curso,
            c.sigla,
            c.programa_etapa1, c.descricao_etapa1,
            c.programa_etapa2, c.descricao_etapa2,
            c.programa_etapa3, c.descricao_etapa3,
            c.programa_etapa4, c.descricao_etapa4,
            c.programa_etapa5, c.descricao_etapa5,
            c.programa_etapa6, c.descricao_etapa6
        FROM alunos a
        INNER JOIN cursos c ON c.id = a.curso_id
        WHERE a.nome_completo = :nome_completo
          AND a.matricula = :matricula
        LIMIT 1
    ");

    $stmt->execute([
        ':nome_completo' => $nome,
        ':matricula' => $matricula,
    ]);

    $registro = $stmt->fetch();

    if (!$registro) {
        $erro = 'Nenhum certificado foi encontrado com os dados informados.';
    } else {
        $statusCertificado = strtolower(trim((string) ($registro['certificado_status'] ?? $registro['status_certificado'] ?? '')));

        if (in_array($statusCertificado, ['pendente', 'inativo'], true)) {
            $bloquearVisualizacao = true;

            $mensagemContato = 'Olá sou "' . $registro['nome_completo'] . '" fiz o curso na cidade "' . $registro['cidade'] . '" no periodo "' . $registro['periodo'] . '" e preciso de informações sobre a pendência do meu certificado, com matrícula "' . $registro['matricula'] . '"';

            $whatsContatoUrl = 'https://wa.me/5577999831980?text=' . rawurlencode($mensagemContato);
        }
    }
}

$pdfUrl = '';
$segundaViaUrl = '';

if ($registro && !$bloquearVisualizacao) {
    $mensagemWhatsapp = 'Olá sou "' . $registro['nome_completo'] . '" fiz o curso na cidade "' . $registro['cidade'] . '" no periodo "' . $registro['periodo'] . '" e preciso de uma segunda via do meu certificado, com matrícula "' . $registro['matricula'] . '"';

    $segundaViaUrl = 'https://wa.me/5577999831980?text=' . rawurlencode($mensagemWhatsapp);
    $pdfUrl = 'gerar_pdf_certificado.php?id=' . (int) $registro['id'];
}

require __DIR__ . '/partials_header.php';
?>

<header class="simple-header">
    <div class="container header-inner">
        <a href="index.php" class="brand"><img src="assets/logo.png" alt="CNHI"></a>
        <a href="index.php" class="btn btn-outline">Voltar</a>
    </div>
</header>

<style>
    .container.narrow {
        max-width: 90%;
        width: 90vw;
    }

    .result-card {
        font-size: 14px;
    }

    .result-columns {
        display: grid;
        grid-template-columns: 30% 70%;
        gap: 20px;
    }

    .result-box {
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 16px;
        background: #fff;
    }

    .result-box h3 {
        margin-bottom: 12px;
        font-size: 16px;
        border-bottom: 1px solid #eee;
        padding-bottom: 8px;
    }

    .result-item {
        padding: 8px 0;
        border-bottom: 1px solid #f1f1f1;
        font-size: 13px;
        line-height: 1.5;
    }

    .result-box:first-child .result-item {
        display: flex;
        justify-content: space-between;
    }

    .result-box:first-child .result-item strong {
        width: 140px;
        font-weight: 600;
    }

    .result-box:nth-child(2) .result-item {
        display: block;
        word-break: break-word;
    }

    .result-box:nth-child(2) .result-item strong {
        display: block;
        margin-bottom: 4px;
        font-weight: 600;
    }

    .consulta-topo {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .consulta-topo h1 {
        margin: 0;
    }

    .consulta-acoes {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    /* Modal */
    .alert-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.55);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        z-index: 9999;
    }

    .alert-modal {
        width: 100%;
        max-width: 520px;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
        overflow: hidden;
        animation: modalFadeIn 0.2s ease;
    }

    .alert-modal-header {
        background: #fff4e5;
        color: #9a3412;
        border-bottom: 1px solid #fed7aa;
        padding: 18px 22px;
        font-size: 20px;
        font-weight: 700;
    }

    .alert-modal-body {
        padding: 22px;
        color: #334155;
        line-height: 1.6;
        font-size: 15px;
    }

    .alert-modal-actions {
        display: flex;
        gap: 12px;
        padding: 0 22px 22px;
        flex-wrap: wrap;
    }

    .alert-modal-actions .btn {
        min-width: 180px;
        text-align: center;
        justify-content: center;
    }

    @keyframes modalFadeIn {
        from {
            opacity: 0;
            transform: translateY(8px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width: 900px) {
        .container.narrow {
            max-width: 95%;
        }

        .result-columns {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 700px) {
        .consulta-topo {
            align-items: flex-start;
            flex-direction: column;
        }
    }
</style>

<main class="section">
    <div class="container narrow">
        <div class="consulta-topo">
            <h1>Consulta de certificado</h1>

            <?php if (!$erro && $registro && !$bloquearVisualizacao): ?>
                <div class="consulta-acoes">
                    <a href="gerar_pdf_certificado.php?id=<?= (int) $registro['id'] ?>" target="_blank" rel="noopener"
                        class="btn btn-primary">
                        Gerar PDF
                    </a>
                    <a href="<?= e($segundaViaUrl) ?>" target="_blank" class="btn btn-outline">Solicitar 2ª via</a>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($erro): ?>
            <div class="result-card error-card">
                <h2>Consulta não localizada</h2>
                <p><?= e($erro) ?></p>
            </div>

        <?php elseif ($bloquearVisualizacao): ?>
            <!-- Não carrega nenhum dado do certificado -->

        <?php else: ?>
            <div class="result-card success-card">
                <h2>Registro localizado com sucesso</h2>

                <div class="result-columns">
                    <div class="result-box">
                        <h3>Informações do Aluno</h3>

                        <div class="result-item"><strong>Matrícula:</strong> <?= e($registro['matricula']) ?></div>
                        <div class="result-item"><strong>Nome:</strong> <?= e($registro['nome_completo']) ?></div>
                        <div class="result-item"><strong>Curso:</strong> <?= e($registro['sigla']) ?></div>
                        <div class="result-item"><strong>Frequência:</strong> <?= e($registro['frequencia']) ?></div>
                        <div class="result-item"><strong>Aproveitamento:</strong> <?= e($registro['aproveitamento']) ?>
                        </div>
                        <div class="result-item"><strong>Período:</strong> <?= e($registro['periodo']) ?></div>
                        <div class="result-item"><strong>Escola:</strong> <?= e($registro['escola']) ?></div>
                        <div class="result-item"><strong>Cidade:</strong> <?= e($registro['cidade']) ?></div>
                    </div>

                    <div class="result-box">
                        <h3>Informações do Curso</h3>

                        <div class="result-item"><strong>Curso:</strong> <?= e($registro['nome_curso']) ?></div>

                        <?php for ($i = 1; $i <= 6; $i++): ?>
                            <?php
                            $programa = $registro["programa_etapa{$i}"] ?? '';
                            $descricao = $registro["descricao_etapa{$i}"] ?? '';
                            ?>
                            <?php if ($programa || $descricao): ?>
                                <div class="result-item">
                                    <strong><?= e($programa) ?>:</strong>
                                    <?= nl2br(e($descricao)) ?>
                                </div>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php if ($bloquearVisualizacao && $registro): ?>
    <div class="alert-modal-overlay" id="alertModal">
        <div class="alert-modal" role="dialog" aria-modal="true" aria-labelledby="alertModalTitle">
            <div class="alert-modal-header" id="alertModalTitle">
                Atenção
            </div>

            <div class="alert-modal-body">
                Foi identificada uma pendência no certificado deste aluno. Por esse motivo, não é possível visualizar os
                dados no momento.
            </div>

            <div class="alert-modal-actions">
                <a href="<?= e($whatsContatoUrl) ?>" target="_blank" class="btn btn-primary">
                    Entrar em contato
                </a>
                <a href="index.php" class="btn btn-outline">Voltar</a>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/partials_footer.php'; ?>