<?php
require_once __DIR__ . '/../config/functions.php';
requireAdmin();
$pageTitle = 'Cursos cadastrados';
$pdo = getPDO();
$search = trim($_GET['search'] ?? '');

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $pdo->prepare('DELETE FROM cursos WHERE id = :id');
    $stmt->execute([':id' => $id]);
    flash('success', 'Curso excluído com sucesso.');
    redirect('cursos.php');
}

if ($search !== '') {
    $stmt = $pdo->prepare("
        SELECT *
        FROM cursos
        WHERE nome_curso LIKE :term
           OR sigla LIKE :term
        ORDER BY id DESC
    ");
    $stmt->execute([':term' => '%' . $search . '%']);
    $cursos = $stmt->fetchAll();
} else {
    $cursos = $pdo->query('SELECT * FROM cursos ORDER BY id DESC')->fetchAll();
}

require __DIR__ . '/_top.php';
?>

<section class="panel-card">
    <div class="panel-actions">
        <form method="get" class="search-inline">
            <input
                type="text"
                name="search"
                placeholder="Buscar por nome do curso ou sigla"
                value="<?= e($search) ?>"
            >
            <button type="submit" class="btn btn-primary">Buscar</button>
        </form>

        <a href="cadastrar_curso.php" class="btn btn-outline">Novo curso</a>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Sigla</th>
                    <th>Nome do curso</th>
                    <th>Etapas preenchidas</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$cursos): ?>
                    <tr>
                        <td colspan="4">Nenhum curso encontrado.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($cursos as $curso): ?>
                        <?php
                        $etapasPreenchidas = 0;
                        for ($i = 1; $i <= 6; $i++) {
                            if (
                                !empty($curso["programa_etapa{$i}"]) ||
                                !empty($curso["descricao_etapa{$i}"])
                            ) {
                                $etapasPreenchidas++;
                            }
                        }
                        ?>
                        <tr>
                            <td><?= e($curso['sigla']) ?></td>
                            <td><?= e($curso['nome_curso']) ?></td>
                            <td><?= e((string) $etapasPreenchidas) ?> / 6</td>
                            <td class="actions-cell">
                                <a href="cadastrar_curso.php?id=<?= (int) $curso['id'] ?>">Editar</a>
                                <a
                                    href="cursos.php?delete=<?= (int) $curso['id'] ?>"
                                    onclick="return confirm('Deseja realmente excluir este curso?');"
                                >
                                    Excluir
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require __DIR__ . '/_bottom.php'; ?>