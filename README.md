# Projeto CNHI

Site institucional + consulta de certificado + painel administrativo em PHP e MySQL.

## O que está incluído
- Página inicial institucional
- Consulta pública de certificado por nome completo e matrícula
- Painel administrativo com login
- Cadastro individual de alunos
- Edição e exclusão de registros
- Importação em lote por CSV
- Script SQL para criação do banco e dados iniciais

## Estrutura principal
- `index.php` → site institucional
- `consulta.php` → retorno da consulta pública
- `admin/login.php` → login administrativo
- `admin/dashboard.php` → painel inicial
- `admin/students.php` → listagem dos alunos
- `admin/student_form.php` → cadastro/edição
- `admin/import.php` → importação em lote
- `sql/cnhi.sql` → criação do banco

## Login padrão de demonstração
- E-mail: `admin@cnhi.com.br`
- Senha: `admin123`

## Como configurar
1. Envie os arquivos para sua hospedagem com suporte a PHP.
2. Crie um banco MySQL.
3. Importe o arquivo `sql/cnhi.sql` no phpMyAdmin.
4. Edite `config/database.php` com host, banco, usuário e senha reais.
5. Acesse o site pela raiz do domínio.

## Observações importantes
- A consulta pública está configurada para localizar o aluno pelo nome completo exato e número da matrícula.
- A importação em lote usa CSV separado por `;`.
- Para produção, altere a senha padrão do administrador imediatamente.
- Se quiser, posso adaptar depois para:
  - upload XLSX em vez de CSV
  - emissão de certificado em PDF
  - QR Code de validação
  - layout com múltiplas páginas institucionais
  - níveis de usuários no painel
