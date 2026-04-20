<?php
require_once __DIR__ . '/../config/functions.php';
$pageTitle = 'Login';
$assetBase = '..';


if (isAdminLoggedIn()) {
    redirect('dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    $pdo = getPDO();
    $stmt = $pdo->prepare('SELECT * FROM admins WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($senha, $admin['senha'])) {
    $_SESSION['admin_id'] = $admin['id'];
    $_SESSION['admin_nome'] = $admin['nome'];
    flash('success', 'Login realizado com sucesso.');
    redirect('dashboard.php');
}

    flash('error', 'E-mail ou senha inválidos.');
    redirect('login.php');
}

require __DIR__ . '/../partials_header.php';
?>
<main class="auth-page">
    <div class="auth-card">
        <img src="../assets/logo.png" alt="CNHI" class="auth-logo">
        <h1>Painel administrativo</h1>
        <p>Acesse para gerenciar registros e certificados.</p>
        <form method="post" class="stack-form">
            <div>
                <label for="email">E-mail</label>
                <input type="email" name="email" id="email" required>
            </div>
            <div>
                <label for="senha">Senha</label>
                <input type="password" name="senha" id="senha" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Entrar</button>
        </form>
    </div>
</main>
<?php require __DIR__ . '/../partials_footer.php'; ?>
