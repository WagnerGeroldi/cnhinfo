<?php
require_once __DIR__ . '/../config/functions.php';
requireAdmin();
$assetBase = '..';
require __DIR__ . '/../partials_header.php';
?>
<div class="admin-layout">
    <aside class="admin-sidebar">
        <img src="../assets/logo.png" alt="CNHI" class="sidebar-logo">
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="students.php">Alunos</a>
            <a href="cursos.php">Cursos</a>
            <a href="import.php">Importar em lote</a>
            <a href="logout.php">Sair</a>
        </nav>
    </aside>
    <main class="admin-main">
        <div class="admin-topbar">
            <div>
                <h1><?= e($pageTitle ?? 'Painel') ?></h1>
                <p>Bem-vindo, <?= e(currentAdminName()) ?>.</p>
            </div>
        </div>
