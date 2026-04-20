<?php
require_once __DIR__ . '/config/functions.php';
require_once __DIR__ . '/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    die('ID inválido.');
}

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
    WHERE a.id = :id
    LIMIT 1
");
$stmt->execute([':id' => $id]);
$registro = $stmt->fetch();

if (!$registro) {
    die('Registro não encontrado.');
}

$logoPath = __DIR__ . '/assets/logo.png';
$logoBase64 = '';

if (file_exists($logoPath)) {
    $logoType = pathinfo($logoPath, PATHINFO_EXTENSION);
    $logoData = file_get_contents($logoPath);
    $logoBase64 = 'data:image/' . $logoType . ';base64,' . base64_encode($logoData);
}

function valor($v): string
{
    return htmlspecialchars((string) ($v ?? ''), ENT_QUOTES, 'UTF-8');
}

$etapasHtml = '';
for ($i = 1; $i <= 6; $i++) {
    $programa = $registro["programa_etapa{$i}"] ?? '';
    $descricao = $registro["descricao_etapa{$i}"] ?? '';

    if ($programa !== '' || $descricao !== '') {
        $etapasHtml .= '
            <div class="course-item">
                <div class="course-title">' . valor($programa) . '</div>
                <div class="course-desc">' . nl2br(valor($descricao)) . '</div>
            </div>
        ';
    }
}

$html = '
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Certificado CNHI</title>
   <style>
    body {
        font-family: DejaVu Sans, sans-serif;
        color: #1f2937;
        font-size: 10px;
        margin: 0;
        padding: 0;
    }

    .page {
        padding: 5px;
    }

    .header {
        width: 100%;
        margin-bottom: 5px;
        border-bottom: 2px solid #1e40af;
        padding-bottom: 5px;
    }

    .header-table {
        width: 100%;
        border-collapse: collapse;
    }

    .header-table td {
        vertical-align: middle;
    }

    .logo {
        width: 110px;
    }

    .title {
        text-align: right;
    }

    .title h1 {
        margin: 0;
        font-size: 20px;
        color: #0f172a;
    }

    .title p {
        margin: 4px 0 0;
        font-size: 11px;
        color: #475569;
    }

    .success-title {
        font-size: 14px;
        font-weight: bold;
        color: #0f172a;
        margin: 8px 0 8px;
    }

    .box {
        border: 1px solid #dbe2ea;
        border-radius: 8px;
        padding: 6px;
        margin-bottom: 6px;
        page-break-inside: auto;
    }

    .box-title {
        font-size: 14px;
        font-weight: bold;
        margin-bottom: 6px;
        color: #0f172a;
        border-bottom: 1px solid #e5e7eb;
        padding-bottom: 6px;
    }

    .info-table {
        width: 100%;
        border-collapse: collapse;
    }

    .info-table tr td {
        padding: 3px 0;
        border-bottom: 1px solid #eef2f7;
        vertical-align: top;
    }

    .info-label {
        width: 28%;
        font-weight: bold;
        color: #111827;
    }

    .info-value {
        width: 72%;
        text-align: left;
    }

    .course-item {
        margin-bottom: 10px;
        padding-bottom: 8px;
        border-bottom: 1px solid #eef2f7;
        page-break-inside: avoid;
    }

    .course-title {
        font-weight: bold;
        margin-bottom: 4px;
        color: #111827;
    }

    .course-desc {
        line-height: 1.4;
    }

    .footer {
        margin-top: 18px;
        border-top: 1px solid #dbe2ea;
        padding-top: 8px;
        font-size: 10px;
        color: #64748b;
        text-align: center;
    }
</style>
</head>
<body>
    <div class="page">
        <div class="header">
            <table class="header-table">
                <tr>
                    <td width="35%">
                        ' . ($logoBase64 ? '<img src="' . $logoBase64 . '" class="logo">' : '') . '
                    </td>
                    <td width="65%" class="title">
                        <h1>Consulta de Certificado</h1>
                        <p>Certificação Nacional de Habilitação em Informática</p>
                    </td>
                </tr>
            </table>
        </div>

        <div class="success-title">Registro localizado com sucesso</div>

        <div class="box" style="margin-bottom: 16px;">
    <div class="box-title">Informações do Aluno</div>

    <table class="info-table">
        <tr>
            <td class="info-label">Matrícula:</td>
            <td class="info-value">' . valor($registro['matricula']) . '</td>
        </tr>
        <tr>
            <td class="info-label">Nome:</td>
            <td class="info-value">' . valor($registro['nome_completo']) . '</td>
        </tr>
        <tr>
            <td class="info-label">Curso:</td>
            <td class="info-value">' . valor($registro['sigla']) . '</td>
        </tr>
        <tr>
            <td class="info-label">Frequência:</td>
            <td class="info-value">' . valor($registro['frequencia']) . '</td>
        </tr>
        <tr>
            <td class="info-label">Aproveitamento:</td>
            <td class="info-value">' . valor($registro['aproveitamento']) . '</td>
        </tr>
        <tr>
            <td class="info-label">Período:</td>
            <td class="info-value">' . valor($registro['periodo']) . '</td>
        </tr>
        <tr>
            <td class="info-label">Escola:</td>
            <td class="info-value">' . valor($registro['escola']) . '</td>
        </tr>
        <tr>
            <td class="info-label">Cidade:</td>
            <td class="info-value">' . valor($registro['cidade']) . '</td>
        </tr>
    </table>
</div>

<div class="box">
    <div class="box-title">Informações do Curso</div>

    <table class="info-table" style="margin-bottom: 12px;">
        <tr>
            <td class="info-label">Curso:</td>
            <td class="info-value">' . valor($registro['nome_curso']) . '</td>
        </tr>
        <tr>
            <td class="info-label">Sigla:</td>
            <td class="info-value">' . valor($registro['sigla']) . '</td>
        </tr>
    </table>

    ' . $etapasHtml . '
</div>

        <div class="footer">
            Documento gerado automaticamente pela plataforma CNHI.
        </div>
    </div>
</body>
</html>
';

$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'DejaVu Sans');

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html, 'UTF-8');
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$nomeArquivo = 'Extrato - ' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $registro['nome_completo']) . '.pdf';

$dompdf->stream($nomeArquivo, ['Attachment' => false]);
exit;