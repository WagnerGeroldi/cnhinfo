<?php
require_once __DIR__ . '/../config/functions.php';
requireAdmin();
$pdo = getPDO();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$editing = $id > 0;
$pageTitle = $editing ? 'Editar curso' : 'Novo curso';

$course = [
    'nome_curso' => '',
    'sigla' => '',

    'programa_etapa1' => '',
    'descricao_etapa1' => '',

    'programa_etapa2' => '',
    'descricao_etapa2' => '',

    'programa_etapa3' => '',
    'descricao_etapa3' => '',

    'programa_etapa4' => '',
    'descricao_etapa4' => '',

    'programa_etapa5' => '',
    'descricao_etapa5' => '',

    'programa_etapa6' => '',
    'descricao_etapa6' => '',
];

if ($editing) {
    $stmt = $pdo->prepare('SELECT * FROM cursos WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $id]);
    $found = $stmt->fetch();

    if (!$found) {
        flash('error', 'Curso não encontrado.');
        redirect('cursos.php');
    }

    $course = $found;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nome_curso' => normalizeText($_POST['nome_curso'] ?? ''),
        'sigla' => normalizeText($_POST['sigla'] ?? ''),

        'programa_etapa1' => normalizeText($_POST['programa_etapa1'] ?? ''),
        'descricao_etapa1' => trim($_POST['descricao_etapa1'] ?? ''),

        'programa_etapa2' => normalizeText($_POST['programa_etapa2'] ?? ''),
        'descricao_etapa2' => trim($_POST['descricao_etapa2'] ?? ''),

        'programa_etapa3' => normalizeText($_POST['programa_etapa3'] ?? ''),
        'descricao_etapa3' => trim($_POST['descricao_etapa3'] ?? ''),

        'programa_etapa4' => normalizeText($_POST['programa_etapa4'] ?? ''),
        'descricao_etapa4' => trim($_POST['descricao_etapa4'] ?? ''),

        'programa_etapa5' => normalizeText($_POST['programa_etapa5'] ?? ''),
        'descricao_etapa5' => trim($_POST['descricao_etapa5'] ?? ''),

        'programa_etapa6' => normalizeText($_POST['programa_etapa6'] ?? ''),
        'descricao_etapa6' => trim($_POST['descricao_etapa6'] ?? ''),
    ];

    if ($data['nome_curso'] === '') {
        flash('error', 'Preencha o nome do curso.');
        redirect($editing ? 'course_form.php?id=' . $id : 'course_form.php');
    }

    if ($editing) {
        $sql = 'UPDATE cursos SET
            nome_curso = :nome_curso,
            sigla = :sigla,
            programa_etapa1 = :programa_etapa1,
            descricao_etapa1 = :descricao_etapa1,
            programa_etapa2 = :programa_etapa2,
            descricao_etapa2 = :descricao_etapa2,
            programa_etapa3 = :programa_etapa3,
            descricao_etapa3 = :descricao_etapa3,
            programa_etapa4 = :programa_etapa4,
            descricao_etapa4 = :descricao_etapa4,
            programa_etapa5 = :programa_etapa5,
            descricao_etapa5 = :descricao_etapa5,
            programa_etapa6 = :programa_etapa6,
            descricao_etapa6 = :descricao_etapa6,
            updated_at = NOW()
            WHERE id = :id';

        $data['id'] = $id;
    } else {
        $sql = 'INSERT INTO cursos (
            nome_curso,
            sigla,
            programa_etapa1, descricao_etapa1,
            programa_etapa2, descricao_etapa2,
            programa_etapa3, descricao_etapa3,
            programa_etapa4, descricao_etapa4,
            programa_etapa5, descricao_etapa5,
            programa_etapa6, descricao_etapa6,
            created_at, updated_at
        ) VALUES (
            :nome_curso,
            :sigla,
            :programa_etapa1, :descricao_etapa1,
            :programa_etapa2, :descricao_etapa2,
            :programa_etapa3, :descricao_etapa3,
            :programa_etapa4, :descricao_etapa4,
            :programa_etapa5, :descricao_etapa5,
            :programa_etapa6, :descricao_etapa6,
            NOW(), NOW()
        )';
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($data);

    flash('success', $editing ? 'Curso atualizado com sucesso.' : 'Curso cadastrado com sucesso.');
    redirect('cursos.php');
}

require __DIR__ . '/_top.php';
?>

<section class="panel-card">
    <form method="post" class="admin-form-grid">
        <div class="full-width">
            <label>Nome do curso</label>
            <input type="text" name="nome_curso" value="<?= e($course['nome_curso']) ?>" required>
        </div>
        <div class="full-width">
            <label>Sigla</label>
            <input type="text" name="sigla" value="<?= e($course['sigla']) ?>" required>
        </div>

        <div>
            <label>Programa etapa 1</label>
            <input type="text" name="programa_etapa1" value="<?= e($course['programa_etapa1']) ?>">
        </div>
        <div>
            <label>Descrição etapa 1</label>
            <textarea name="descricao_etapa1" rows="4"><?= e($course['descricao_etapa1']) ?></textarea>
        </div>

        <div>
            <label>Programa etapa 2</label>
            <input type="text" name="programa_etapa2" value="<?= e($course['programa_etapa2']) ?>">
        </div>
        <div>
            <label>Descrição etapa 2</label>
            <textarea name="descricao_etapa2" rows="4"><?= e($course['descricao_etapa2']) ?></textarea>
        </div>

        <div>
            <label>Programa etapa 3</label>
            <input type="text" name="programa_etapa3" value="<?= e($course['programa_etapa3']) ?>">
        </div>
        <div>
            <label>Descrição etapa 3</label>
            <textarea name="descricao_etapa3" rows="4"><?= e($course['descricao_etapa3']) ?></textarea>
        </div>

        <div>
            <label>Programa etapa 4</label>
            <input type="text" name="programa_etapa4" value="<?= e($course['programa_etapa4']) ?>">
        </div>
        <div>
            <label>Descrição etapa 4</label>
            <textarea name="descricao_etapa4" rows="4"><?= e($course['descricao_etapa4']) ?></textarea>
        </div>

        <div>
            <label>Programa etapa 5</label>
            <input type="text" name="programa_etapa5" value="<?= e($course['programa_etapa5']) ?>">
        </div>
        <div>
            <label>Descrição etapa 5</label>
            <textarea name="descricao_etapa5" rows="4"><?= e($course['descricao_etapa5']) ?></textarea>
        </div>

        <div>
            <label>Programa etapa 6</label>
            <input type="text" name="programa_etapa6" value="<?= e($course['programa_etapa6']) ?>">
        </div>
        <div>
            <label>Descrição etapa 6</label>
            <textarea name="descricao_etapa6" rows="4"><?= e($course['descricao_etapa6']) ?></textarea>
        </div>

        <div class="full-width form-actions">
            <button type="submit" class="btn btn-primary">Salvar curso</button>
            <a href="cursos.php" class="btn btn-light">Cancelar</a>
        </div>
    </form>
</section>

<?php require __DIR__ . '/_bottom.php'; ?>