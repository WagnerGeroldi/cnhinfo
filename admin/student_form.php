<?php
require_once __DIR__ . '/../config/functions.php';
requireAdmin();
$pdo = getPDO();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$editing = $id > 0;
$pageTitle = $editing ? 'Editar aluno' : 'Novo aluno';

$coursesStmt = $pdo->query('SELECT id, sigla FROM cursos ORDER BY sigla ASC');
$courses = $coursesStmt->fetchAll();

$student = [
    'nome_completo' => '',
    'matricula' => '',
    'curso_id' => '',
    'cidade' => '',
    'escola' => '',
    'periodo' => '',
    'frequencia' => '',
    'aproveitamento' => '',
    'status_certificado' => 'Ativo',
    'registration_code' => generateRegistrationNumber(),
    'notes' => '',
];

if ($editing) {
    $stmt = $pdo->prepare('SELECT * FROM alunos WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $id]);
    $found = $stmt->fetch();

    if (!$found) {
        flash('error', 'Cadastro não encontrado.');
        redirect('alunos.php');
    }

    $student = array_merge($student, $found);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nome_completo' => normalizeText($_POST['nome_completo'] ?? ''),
        'matricula' => normalizeText($_POST['matricula'] ?? ''),
        'curso_id' => (int) ($_POST['curso_id'] ?? 0),
        'cidade' => normalizeText($_POST['cidade'] ?? ''),
        'escola' => normalizeText($_POST['escola'] ?? ''),
        'periodo' => normalizeText($_POST['periodo'] ?? ''),
        'frequencia' => (float) str_replace(',', '.', ($_POST['frequencia'] ?? 0)),
        'aproveitamento' => (float) str_replace(',', '.', ($_POST['aproveitamento'] ?? 0)),
        'status_certificado' => normalizeText($_POST['status_certificado'] ?? 'Ativo'),
    ];

    if ($data['nome_completo'] === '' || $data['matricula'] === '' || $data['curso_id'] <= 0) {
        flash('error', 'Preencha pelo menos nome, matrícula e curso.');
        redirect($editing ? 'student_form.php?id=' . $id : 'student_form.php');
    }

    if ($editing) {
        $sql = 'UPDATE alunos SET
            nome_completo = :nome_completo,
            matricula = :matricula,
            curso_id = :curso_id,
            cidade = :cidade,
            escola = :escola,
            periodo = :periodo,
            frequencia = :frequencia,
            aproveitamento = :aproveitamento,
            status_certificado = :status_certificado,
            updated_at = NOW()
            WHERE id = :id';

        $data['id'] = $id;
    } else {
        $sql = 'INSERT INTO alunos (
            nome_completo,
            matricula,
            curso_id,
            cidade,
            escola,
            periodo,
            frequencia,
            aproveitamento,
            status_certificado,
            created_at,
            updated_at
        ) VALUES (
            :nome_completo,
            :matricula,
            :curso_id,
            :cidade,
            :escola,
            :periodo,
            :frequencia,
            :aproveitamento,
            :status_certificado,
            NOW(),
            NOW()
        )';
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($data);

    flash('success', $editing ? 'Cadastro atualizado com sucesso.' : 'Aluno cadastrado com sucesso.');
    redirect('dashboard.php');
}

require __DIR__ . '/_top.php';
?>

<section class="panel-card">
    <form method="post" class="admin-form-grid">
        <div>
            <label>Nome completo</label>
            <input type="text" name="nome_completo" value="<?= e($student['nome_completo']) ?>" required>
        </div>

        <div>
            <label>Matrícula</label>
            <input type="text" name="matricula" value="<?= e($student['matricula']) ?>" required>
        </div>

        <div>
            <label>Curso</label>
            <select name="curso_id" required>
                <option value="">Selecione um curso</option>
                <?php foreach ($courses as $course): ?>
                    <option value="<?= (int) $course['id'] ?>" <?= (int) ($student['curso_id'] ?? 0) === (int) $course['id'] ? 'selected' : '' ?>>
                        <?= e($course['sigla']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label>Cidade</label>
            <input type="text" name="cidade" value="<?= e($student['cidade']) ?>">
        </div>

        <div>
            <label>Escola</label>
            <input type="text" name="escola" value="<?= e($student['escola']) ?>">
        </div>

        <div>
            <label>Período</label>
            <input type="text" name="periodo" value="<?= e($student['periodo']) ?>">
        </div>

        <div>
            <label>Frequência</label>
            <input
                type="text"
                name="frequencia"
                value="<?= e((string) $student['frequencia']) ?>"
                inputmode="decimal"
                pattern="[0-9]+([,\.][0-9]+)?"
                placeholder="0,00"
                oninput="this.value = this.value.replace(/[^0-9,\.]/g, '').replace(/(\..*)\./g, '$1');"
            >
        </div>

        <div>
            <label>Aproveitamento</label>
            <input
                type="text"
                name="aproveitamento"
                value="<?= e((string) $student['aproveitamento']) ?>"
                inputmode="decimal"
                pattern="[0-9]+([,\.][0-9]+)?"
                placeholder="0,00"
                oninput="this.value = this.value.replace(/[^0-9,\.]/g, '').replace(/(\..*)\./g, '$1');"
            >
        </div>

        <div>
            <label>Status</label>
            <select name="status_certificado">
                <?php foreach (['Ativo', 'Inativo', 'Pendente'] as $status): ?>
                    <option value="<?= e($status) ?>" <?= ($student['status_certificado'] ?? 'Ativo') === $status ? 'selected' : '' ?>>
                        <?= e($status) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="full-width form-actions">
            <button type="submit" class="btn btn-primary">Salvar cadastro</button>
            <a href="students.php" class="btn btn-light">Cancelar</a>
        </div>
    </form>
</section>

<?php require __DIR__ . '/_bottom.php'; ?>