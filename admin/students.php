<?php
require_once __DIR__ . '/../config/functions.php';
requireAdmin();

$pageTitle = 'Alunos cadastrados';
$pdo = getPDO();

$search = trim($_GET['search'] ?? '');
$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 10;
$offset = ($page - 1) * $perPage;

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $pdo->prepare('DELETE FROM alunos WHERE id = :id');
    $stmt->execute([':id' => $id]);
    flash('success', 'Cadastro excluído com sucesso.');
    redirect('students.php' . ($search !== '' ? '?search=' . urlencode($search) : ''));
}

if ($search !== '') {
    $countStmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM alunos a
        LEFT JOIN cursos c ON c.id = a.curso_id
        WHERE a.nome_completo LIKE :term
           OR a.matricula LIKE :term
           OR a.cidade LIKE :term
           OR c.sigla LIKE :term
           OR c.nome_curso LIKE :term
    ");
    $countStmt->execute([':term' => '%' . $search . '%']);
    $totalRecords = (int) $countStmt->fetchColumn();

    $stmt = $pdo->prepare("
        SELECT 
            a.*,
            c.sigla,
            c.nome_curso
        FROM alunos a
        LEFT JOIN cursos c ON c.id = a.curso_id
        WHERE a.nome_completo LIKE :term
           OR a.matricula LIKE :term
           OR a.cidade LIKE :term
           OR c.sigla LIKE :term
           OR c.nome_curso LIKE :term
        ORDER BY a.id DESC
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindValue(':term', '%' . $search . '%', PDO::PARAM_STR);
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $alunos = $stmt->fetchAll();
} else {
    $countStmt = $pdo->query("SELECT COUNT(*) FROM alunos");
    $totalRecords = (int) $countStmt->fetchColumn();

    $stmt = $pdo->prepare("
        SELECT 
            a.*,
            c.sigla,
            c.nome_curso
        FROM alunos a
        LEFT JOIN cursos c ON c.id = a.curso_id
        ORDER BY a.id DESC
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $alunos = $stmt->fetchAll();
}

$totalPages = max(1, (int) ceil($totalRecords / $perPage));

require __DIR__ . '/_top.php';
?>

<section class="panel-card">
    <div class="panel-actions">
        <form method="get" class="search-inline">
            <input
                type="text"
                name="search"
                placeholder="Buscar por nome, matrícula, cidade ou curso"
                value="<?= e($search) ?>"
            >
            <button type="submit" class="btn btn-primary">Buscar</button>
        </form>

        <a href="student_form.php" class="btn btn-outline">Novo aluno</a>
    </div>

    <div style="margin-bottom: 16px;">
        <small>
            Total de registros: <?= (int) $totalRecords ?>
            <?php if ($search !== ''): ?>
                | Resultado da busca por: <strong><?= e($search) ?></strong>
            <?php endif; ?>
        </small>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Matrícula</th>
                    <th>Cidade</th>
                    <th>Curso</th>
                    <th>Aproveitamento</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$alunos): ?>
                    <tr>
                        <td colspan="7">Nenhum registro encontrado.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($alunos as $student): ?>
                        <tr>
                            <td><?= e($student['nome_completo']) ?></td>
                            <td><?= e($student['matricula']) ?></td>
                            <td><?= e($student['cidade']) ?></td>
                            <td><?= e($student['sigla'] ?: $student['curso_id']) ?></td>
                            <td><?= e((string) $student['aproveitamento']) ?></td>
                            <td><?= e($student['status_certificado'] ?? $student['status_certificado'] ?? '') ?></td>
                            <td class="actions-cell">
                                <a href="student_form.php?id=<?= (int) $student['id'] ?>">Editar</a>
                                <a href="students.php?delete=<?= (int) $student['id'] ?><?= $search !== '' ? '&search=' . urlencode($search) : '' ?>" onclick="return confirm('Deseja realmente excluir este cadastro?');">Excluir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
        <div class="pagination" style="margin-top: 20px; display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
            <?php
            $queryBase = [];
            if ($search !== '') {
                $queryBase['search'] = $search;
            }
            ?>

            <?php if ($page > 1): ?>
                <?php $prevQuery = http_build_query(array_merge($queryBase, ['page' => $page - 1])); ?>
                <a href="students.php?<?= e($prevQuery) ?>" class="btn btn-light">Anterior</a>
            <?php endif; ?>

            <?php
            $startPage = max(1, $page - 2);
            $endPage = min($totalPages, $page + 2);
            ?>

            <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                <?php $pageQuery = http_build_query(array_merge($queryBase, ['page' => $i])); ?>
                <?php if ($i === $page): ?>
                    <span class="btn btn-primary"><?= $i ?></span>
                <?php else: ?>
                    <a href="students.php?<?= e($pageQuery) ?>" class="btn btn-light"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <?php $nextQuery = http_build_query(array_merge($queryBase, ['page' => $page + 1])); ?>
                <a href="students.php?<?= e($nextQuery) ?>" class="btn btn-light">Próxima</a>
            <?php endif; ?>

            <span style="margin-left: 8px;">
                Página <?= (int) $page ?> de <?= (int) $totalPages ?>
            </span>
        </div>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/_bottom.php'; ?>