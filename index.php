<?php
require_once __DIR__ . '/config/functions.php';
$pageTitle = 'Início';
$assetBase = '.';
require __DIR__ . '/partials_header.php';
?>
<header class="site-header">
    <div class="container header-inner">
        <a href="index.php" class="brand">
            <img src="assets/logo.png" alt="Logo CNHI">
        </a>
        <nav class="menu">
            <a href="#sobre">Sobre</a>
            <a href="#como-funciona">Como funciona</a>
            <a href="#consulta">Consultar certificado</a>
            <a href="#contato">Contato</a>
            <a href="admin/login.php" class="btn btn-outline">Painel</a>
        </nav>
    </div>
</header>

<main>
    <section class="hero">
        <div class="container hero-grid">
            <div>
                <span class="eyebrow">Certificação, validação e credibilidade</span>
                <h1>Registro nacional para cursos livres de informática com consulta rápida e segura.</h1>
                <p>A CNHI atua como certificadora de cursos livres de informática, mantendo um banco de registros confiável para que alunos, instituições e interessados possam consultar certificados com transparência e segurança.</p>
                <div class="hero-actions">
                    <a href="#consulta" class="btn btn-primary">Consultar certificado</a>
                    <a href="#sobre" class="btn btn-light">Conhecer a CNHI</a>
                </div>
            </div>
            <div class="hero-card">
                <h3>Por que a CNHI?</h3>
                <ul>
                    <li>Registro centralizado de alunos certificados</li>
                    <li>Consulta pública por nome completo e matrícula</li>
                    <li>Ambiente institucional com foco em credibilidade</li>
                </ul>
            </div>
        </div>
    </section>

    <section id="sobre" class="section">
        <div class="container narrow">
            <h2>Sobre a CNHI</h2>
            <p>A <strong>Certificação Nacional de Habilitação em Informática</strong> é uma certificadora voltada ao registro e à validação de cursos livres de informática. Seu papel é organizar informações acadêmicas essenciais, fortalecendo a confiabilidade dos certificados emitidos e facilitando a consulta por parte dos estudantes.</p>
            <p>Com uma proposta simples e profissional, a CNHI oferece uma base segura para armazenamento de dados de formação, permitindo que a autenticidade dos registros seja verificada de forma prática.</p>
        </div>
    </section>

    <section id="como-funciona" class="section section-alt">
        <div class="container">
            <h2>Como funciona</h2>
            <div class="cards-3">
                <article class="info-card">
                    <span class="step">01</span>
                    <h3>Cadastro</h3>
                    <p>Os Alunos que concluem o curso e recebem a aprovação, registram seu certificado para que possa ser consultado e validado em qualquer parte do território nacional.</p>
                </article>
                <article class="info-card">
                    <span class="step">02</span>
                    <h3>Registro</h3>
                    <p>Cada aluno recebe um número de registro, com informações de curso, cidade, nota e situação do certificado.</p>
                </article>
                <article class="info-card">
                    <span class="step">03</span>
                    <h3>Consulta</h3>
                    <p>O aluno informa nome completo e matrícula para localizar seu certificado e confirmar a validade do registro.</p>
                </article>
            </div>
        </div>
    </section>

    <section id="consulta" class="section">
        <div class="container narrow">
            <h2>Consultar certificado</h2>
            <p>Informe o nome completo e o número da matrícula para verificar o registro do certificado.</p>
            <form action="consulta.php" method="post" class="search-form">
                <div class="form-grid">
                    <div>
                        <label for="nome_completo">Nome completo</label>
                        <input type="text" name="nome_completo" id="nome_completo" required>
                    </div>
                    <div>
                        <label for="matricula">Número da matrícula</label>
                        <input type="text" name="matricula" id="matricula" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Consultar agora</button>
            </form>
        </div>
    </section>

    <section class="section section-alt">
        <div class="container">
            <h2>Vantagens da CNHI</h2>
            <div class="cards-3">
                <article class="info-card">
                    <h3>Missão</h3>
                    <p>Excelência de ensino, manter o padrão das melhores práticas em ensino de informática.</p>
                </article>
                <article class="info-card">
                    <h3>Visão</h3>
                    <p>Promover a segurança e qualidade dos cursos, fazendo que o registro seja uma ferramenta essencial na vida do estudante.</p>
                </article>
                <article class="info-card">
                    <h3>Valores</h3>
                    <p>O conhecimento é a base da sociedade, aliado à habilidade, constrói uma sociedade melhor!</p>
                </article>
            </div>
        </div>
    </section>
</main>

<footer id="contato" class="site-footer">
    <div class="container footer-grid">
        <div>
            <img src="assets/logo.png" alt="CNHI" class="footer-logo">
            <p>Certificação Nacional de Habilitação em Informática</p>
        </div>
        <div>
            <h4>Contato</h4>
            <p>E-mail: contato@cnhinfo.com.br</p>
        </div>
        <div>
            <h4>Acesso rápido</h4>
            <p><a href="#consulta">Consulta de certificado</a></p>
            <p><a href="admin/login.php">Painel administrativo</a></p>
        </div>
    </div>
</footer>
<?php require __DIR__ . '/partials_footer.php'; ?>
