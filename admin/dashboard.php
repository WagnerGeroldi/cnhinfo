<?php
require_once __DIR__ . '/../config/functions.php';
requireAdmin();
$pageTitle = 'Dashboard';
$pdo = getPDO();
$totalAlunos = (int) $pdo->query('SELECT COUNT(*) FROM alunos')->fetchColumn();
$activeCertificates = (int) $pdo->query("SELECT COUNT(*) FROM alunos WHERE status_certificado = 'Ativo'")->fetchColumn();
$totalCourses = (int) $pdo->query('SELECT COUNT(*) FROM cursos')->fetchColumn();
$recentAlunos = $pdo->query('SELECT * FROM alunos ORDER BY id DESC LIMIT 5')->fetchAll();
require __DIR__ . '/_top.php';
?>
<div class="stats-grid">
    <div class="stat-card"><span>Total de alunos</span><strong><?= $totalAlunos ?></strong></div>
    <div class="stat-card"><span>Certificados ativos</span><strong><?= $activeCertificates ?></strong></div>
    <div class="stat-card"><span>Cursos cadastrados</span><strong><?= $totalCourses ?></strong></div>
</div>

<section class="panel-card">
    <h2>Cadastros recentes</h2>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Matrícula</th>
                    <th>Curso</th>
                    <th>Cidade</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentAlunos as $student): ?>
                    <tr>
                        <td><?= e($student['nome_completo']) ?></td>
                        <td><?= e($student['matricula']) ?></td>
                        <td><?= e($student['curso_id']) ?></td>
                        <td><?= e($student['cidade']) ?></td>
                        <td><?= e($student['status_certificado']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php require __DIR__ . '/_bottom.php'; ?>
