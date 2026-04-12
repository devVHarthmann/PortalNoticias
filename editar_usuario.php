<?php
session_start();
require_once 'verifica_login.php';
require_once 'config/config.php';
require_once 'funcoes.php';
require_once 'classes/Usuario.php';

$ehAdmin = !empty($_SESSION['usuario_admin']);
$idAlvo  = (int)($_GET['id'] ?? $_POST['id'] ?? 0);

// Usuário comum só pode editar a própria conta
if (!$ehAdmin && $idAlvo != $_SESSION['usuario_id']) {
    header('Location: dashboard.php'); exit;
}
// Admin editando conta própria usa seu próprio id
if (!$idAlvo) $idAlvo = $_SESSION['usuario_id'];

$usuarioObj = new Usuario($db);
$usuario    = $usuarioObj->lerPorId($idAlvo);
if (!$usuario) { header('Location: $ehAdmin ? usuarios.php : dashboard.php'); exit; }

$erro    = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome     = trim($_POST['nome']     ?? '');
    $email    = trim($_POST['email']    ?? '');
    $telefone = trim($_POST['telefone'] ?? '');

    if (!$nome || !$email) {
        $erro = 'Nome e e-mail são obrigatórios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'E-mail inválido.';
    } else {
        try {
            $usuarioObj->atualizar($idAlvo, $nome, $email, $telefone);
            // Atualiza sessão se for o próprio usuário
            if ($idAlvo == $_SESSION['usuario_id']) {
                $_SESSION['usuario_nome'] = $nome;
            }
            $usuario['nome']     = $nome;
            $usuario['email']    = $email;
            $usuario['telefone'] = $telefone;
            $sucesso = 'Perfil atualizado com sucesso!';
        } catch (PDOException $e) {
            $erro = str_contains($e->getMessage(), 'Duplicate')
                    ? 'Este e-mail já está em uso por outra conta.'
                    : 'Erro ao atualizar. Tente novamente.';
        }
    }
}

$voltarUrl = ($ehAdmin && $idAlvo != $_SESSION['usuario_id']) ? 'usuarios.php' : 'dashboard.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editar Perfil — TechPortal</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="site-header">
  <div class="container">
    <div class="header-inner">
      <a href="index.php" class="site-logo">Tech<span>Portal</span><small>Tecnologia &amp; Inovação</small></a>
      <nav class="header-nav">
        <a href="dashboard.php">Painel</a>
        <a href="index.php">Portal</a>
        <a href="logout.php">Sair</a>
      </nav>
    </div>
  </div>
</header>
<div class="strip"></div>

<div class="page-wrap">
  <div class="container--narrow">
    <h1 class="page-title">
      Editar Perfil
      <?php if ($ehAdmin && $idAlvo != $_SESSION['usuario_id']): ?>
        <small style="font-size:.6em;font-family:var(--ff-body);color:var(--accent)">
          — <?= htmlspecialchars($usuario['nome']) ?>
        </small>
      <?php endif; ?>
    </h1>

    <?php if ($ehAdmin && $idAlvo != $_SESSION['usuario_id']): ?>
      <div class="alert alert-info" style="margin-bottom:1rem">
        Você está editando a conta de outro usuário como administrador.
      </div>
    <?php endif; ?>

    <?php if ($erro): ?>
      <div class="alert alert-error"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>
    <?php if ($sucesso): ?>
      <div class="alert alert-success"><?= htmlspecialchars($sucesso) ?></div>
    <?php endif; ?>

    <div class="form-card">
      <form method="POST">
        <input type="hidden" name="id" value="<?= $idAlvo ?>">
        <div class="form-group">
          <label class="form-label">Nome completo *</label>
          <input type="text" name="nome" class="form-control"
                 value="<?= htmlspecialchars($usuario['nome']) ?>" required>
        </div>
        <div class="form-group">
          <label class="form-label">E-mail *</label>
          <input type="email" name="email" class="form-control"
                 value="<?= htmlspecialchars($usuario['email']) ?>" required>
        </div>
        <div class="form-group">
          <label class="form-label">Telefone</label>
          <input type="text" name="telefone" class="form-control"
                 value="<?= htmlspecialchars($usuario['telefone'] ?? '') ?>"
                 placeholder="(00) 00000-0000">
        </div>
        <div class="d-flex gap-2 mt-2 flex-wrap">
          <button type="submit" class="btn btn-primary btn-lg">Salvar Alterações</button>
          <a href="<?= $voltarUrl ?>" class="btn btn-outline btn-lg">Cancelar</a>
        </div>
      </form>
    </div>

    <!-- Bloco separado: alterar senha -->
    <div class="form-card mt-3">
      <h3 style="font-family:var(--ff-head);font-size:1.1rem;font-weight:700;margin-bottom:.4rem">Alterar Senha</h3>
      <p class="text-muted" style="margin-bottom:1rem">
        <?= $ehAdmin && $idAlvo != $_SESSION['usuario_id']
            ? 'Redefina a senha deste usuário sem precisar informar a atual.'
            : 'Para alterar sua senha, clique no botão abaixo.' ?>
      </p>
      <a href="alterar_senha.php?id=<?= $idAlvo ?>" class="btn btn-outline">🔑 Alterar senha</a>
    </div>

    <!-- Bloco exclusão -->
    <?php if ($idAlvo == $_SESSION['usuario_id'] || $ehAdmin): ?>
    <div class="form-card mt-3" style="border-color:#fca5a5">
      <h3 style="font-family:var(--ff-head);font-size:1.1rem;font-weight:700;color:var(--accent);margin-bottom:.4rem">Zona de Perigo</h3>
      <p class="text-muted" style="margin-bottom:1rem">
        A exclusão da conta é permanente e remove também todas as notícias associadas.
      </p>
      <a href="excluir_usuario.php?id=<?= $idAlvo ?>"
         class="btn btn-danger"
         data-confirm="Esta ação é irreversível. A conta e todas as notícias serão excluídas."
         data-confirm-title="Excluir conta?"
         data-confirm-icon="⚠️"
         data-confirm-label="Sim, excluir tudo">
        Excluir esta conta
      </a>
    </div>
    <?php endif; ?>

  </div>
</div>

<footer class="site-footer">
  <div class="container">
    <p><strong>TechPortal</strong> — <a href="index.php">Portal</a></p>
  </div>
</footer>

<script src="modal.js"></script>
</body>
</html>
