<?php
require_once __DIR__ . '/../config/functions.php';
requireAdmin();
$pageTitle = 'Importação em lote';
$pdo = getPDO();

function normalizarCsvValor(string $valor): string
{
    $valor = preg_replace('/^\xEF\xBB\xBF/', '', $valor);
    return trim($valor);
}

function normalizarCabecalhoCsv(string $valor): string
{
    $valor = normalizarCsvValor($valor);
    $valor = mb_strtolower($valor, 'UTF-8');

    $mapa = [
        'á' => 'a', 'à' => 'a', 'ã' => 'a', 'â' => 'a', 'ä' => 'a',
        'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
        'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i',
        'ó' => 'o', 'ò' => 'o', 'õ' => 'o', 'ô' => 'o', 'ö' => 'o',
        'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u',
        'ç' => 'c',
    ];

    $valor = strtr($valor, $mapa);
    $valor = preg_replace('/[^a-z0-9]+/u', '_', $valor);
    $valor = trim($valor, '_');

    $aliases = [
        'nome' => 'nome',
        'i_nome' => 'nome',
        'nome_completo' => 'nome',
        'nome_do_aluno' => 'nome',
        'aluno' => 'nome',

        'matricula' => 'matricula',
        'cidade' => 'cidade',
        'escola' => 'escola',
        'periodo' => 'periodo',
        'curso_id' => 'curso_id',
        'curso' => 'curso_id',
        'frequencia' => 'frequencia',
        'freq' => 'frequencia',
        'aproveitamento' => 'aproveitamento',
        'nota' => 'aproveitamento',
    ];

    return $aliases[$valor] ?? $valor;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    $file = $_FILES['csv_file'];

    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        flash('error', 'Falha ao enviar o arquivo CSV.');
        redirect('import.php');
    }

    $handle = fopen($file['tmp_name'], 'r');
    if ($handle === false) {
        flash('error', 'Não foi possível ler o arquivo enviado.');
        redirect('import.php');
    }

    $header = fgetcsv($handle, 0, ';');

    if (!$header) {
        fclose($handle);
        flash('error', 'O arquivo CSV está vazio.');
        redirect('import.php');
    }

    $header = array_map(
        fn ($item) => normalizarCabecalhoCsv((string) $item),
        $header
    );

    if (isset($header[0]) && $header[0] === 'sep') {
        $header = fgetcsv($handle, 0, ';');

        if (!$header) {
            fclose($handle);
            flash('error', 'O arquivo CSV não possui cabeçalho válido.');
            redirect('import.php');
        }

        $header = array_map(
            fn ($item) => normalizarCabecalhoCsv((string) $item),
            $header
        );
    }

    $camposObrigatorios = [
        'nome',
        'matricula',
        'cidade',
        'escola',
        'periodo',
        'curso_id',
        'frequencia',
        'aproveitamento',
    ];

    $colunasFaltandoNoCabecalho = [];
    foreach ($camposObrigatorios as $campo) {
        if (!in_array($campo, $header, true)) {
            $colunasFaltandoNoCabecalho[] = $campo;
        }
    }

    if (!empty($colunasFaltandoNoCabecalho)) {
        fclose($handle);
        flash(
            'error',
            'O CSV não contém as colunas obrigatórias: ' . implode(', ', $colunasFaltandoNoCabecalho) .
            '. Cabeçalho lido: ' . implode(', ', $header)
        );
        redirect('import.php');
    }

    $linhasParaImportar = [];
    $linhaCsv = 1;

    while (($row = fgetcsv($handle, 0, ';')) !== false) {
        $linhaCsv++;

        $row = array_map(function ($value) {
            return trim((string) $value);
        }, $row);

        $linhaVazia = true;
        foreach ($row as $valor) {
            if ($valor !== '') {
                $linhaVazia = false;
                break;
            }
        }

        if ($linhaVazia) {
            continue;
        }

        if (count($row) !== count($header)) {
            fclose($handle);
            flash('error', "Erro na linha {$linhaCsv}: quantidade de colunas diferente do cabeçalho do CSV.");
            redirect('import.php');
        }

        $data = array_combine($header, $row);

        if ($data === false) {
            fclose($handle);
            flash('error', "Erro na linha {$linhaCsv}: não foi possível interpretar os dados do CSV.");
            redirect('import.php');
        }

        $camposVazios = [];
        foreach ($camposObrigatorios as $campo) {
            $valor = trim((string) ($data[$campo] ?? ''));
            if ($valor === '') {
                $camposVazios[] = $campo;
            }
        }

        if (!empty($camposVazios)) {
            fclose($handle);
            flash(
                'error',
                "Erro na linha {$linhaCsv}: os seguintes campos estão vazios: " . implode(', ', $camposVazios) . '. Nenhum registro foi importado.'
            );
            redirect('import.php');
        }

        if (!is_numeric(str_replace(',', '.', (string) $data['curso_id']))) {
            fclose($handle);
            flash('error', "Erro na linha {$linhaCsv}: o campo curso_id deve ser numérico. Nenhum registro foi importado.");
            redirect('import.php');
        }

        if (!is_numeric(str_replace(',', '.', (string) $data['frequencia']))) {
            fclose($handle);
            flash('error', "Erro na linha {$linhaCsv}: o campo frequencia deve ser numérico. Nenhum registro foi importado.");
            redirect('import.php');
        }

        if (!is_numeric(str_replace(',', '.', (string) $data['aproveitamento']))) {
            fclose($handle);
            flash('error', "Erro na linha {$linhaCsv}: o campo aproveitamento deve ser numérico. Nenhum registro foi importado.");
            redirect('import.php');
        }

        $linhasParaImportar[] = [
            'nome_completo'  => normalizeText((string) $data['nome']),
            'matricula'      => normalizeText((string) $data['matricula']),
            'cidade'         => normalizeText((string) $data['cidade']),
            'escola'         => normalizeText((string) $data['escola']),
            'periodo'        => normalizeText((string) $data['periodo']),
            'curso_id'       => (int) str_replace(',', '.', (string) $data['curso_id']),
            'frequencia'     => (float) str_replace(',', '.', (string) $data['frequencia']),
            'aproveitamento' => (float) str_replace(',', '.', (string) $data['aproveitamento']),
        ];
    }

    fclose($handle);

    if (empty($linhasParaImportar)) {
        flash('error', 'Nenhuma linha válida foi encontrada no CSV.');
        redirect('import.php');
    }

    $sql = 'INSERT INTO alunos (
                nome_completo,
                matricula,
                cidade,
                escola,
                periodo,
                curso_id,
                frequencia,
                aproveitamento,
                created_at,
                updated_at
            ) VALUES (
                :nome_completo,
                :matricula,
                :cidade,
                :escola,
                :periodo,
                :curso_id,
                :frequencia,
                :aproveitamento,
                NOW(),
                NOW()
            )';

    $stmt = $pdo->prepare($sql);

    try {
        $pdo->beginTransaction();

        foreach ($linhasParaImportar as $linha) {
            $stmt->execute([
                ':nome_completo'  => $linha['nome_completo'],
                ':matricula'      => $linha['matricula'],
                ':cidade'         => $linha['cidade'],
                ':escola'         => $linha['escola'],
                ':periodo'        => $linha['periodo'],
                ':curso_id'       => $linha['curso_id'],
                ':frequencia'     => $linha['frequencia'],
                ':aproveitamento' => $linha['aproveitamento'],
            ]);
        }

        $pdo->commit();

        $total = count($linhasParaImportar);
        flash('success', "Importação concluída com sucesso. {$total} registro(s) importado(s).");
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        flash('error', 'Erro ao importar os dados. Nenhum registro foi salvo.');
    }

    redirect('students.php');
}

require __DIR__ . '/_top.php';
?>

<section class="panel-card">
    <h2>Importar alunos por CSV</h2>
    <p>Envie um arquivo CSV conforme o modelo abaixo. Salve no formato CSV separado por <strong>;</strong>.</p>

    <div class="modelo-csv-box">
        <img src="../assets/exemploCSV.png" alt="Modelo CSV">
        <a href="../assets/modelo_importacao.csv" download class="btn-download-csv">⬇ Baixar modelo CSV</a>
    </div>

    <form method="post" enctype="multipart/form-data" class="stack-form">
        <div>
            <label>Arquivo CSV</label>
            <input type="file" name="csv_file" accept=".csv" required>
        </div>

        <button type="submit" class="btn btn-primary">Importar agora</button>
    </form>
</section>

<?php require __DIR__ . '/_bottom.php'; ?>