<?php
session_start();
require_once 'verifica_login.php';
require_once 'config/config.php';
require_once 'funcoes.php';
require_once 'classes/Usuario.php';

// Admin pode alterar senha de qualquer usuário; usuário comum só a própria
$idAlvo = (int)($_GET['id'] ?? $_POST['id'] ?? $_SESSION['usuario_id']);
$ehAdmin = !empty($_SESSION['usuario_admin']);

if (!$ehAdmin && $idAlvo != $_SESSION['usuario_id']) {
    header('Location: dashboard.php'); exit;
}

$usuarioObj = new Usuario($db);
$alvo       = $usuarioObj->lerPorId($idAlvo);
if (!$alvo) { header('Location: dashboard.php'); exit; }

$erro    = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $senhaAtual  = $_POST['senha_atual']  ?? '';
    $novaSenha   = $_POST['nova_senha']   ?? '';
    $confirma    = $_POST['confirma']     ?? '';

    if (strlen($novaSenha) < 6) {
        $erro = 'A nova senha deve ter pelo menos 6 caracteres.';
    } elseif ($novaSenha !== $confirma) {
        $erro = 'A confirmação não coincide com a nova senha.';
    } elseif (!$ehAdmin) {
        // Usuário comum precisa confirmar a senha atual
        if (!password_verify($senhaAtual, $alvo['senha'])) {
            $erro = 'Senha atual incorreta.';
        }
    }

    if (!$erro) {
        $usuarioObj->alterarSenha($idAlvo, $novaSenha);
        $sucesso = 'Senha alterada com sucesso!';
    }
}

$voltarUrl = $ehAdmin && $idAlvo != $_SESSION['usuario_id']
    ? 'usuarios.php'
    : 'editar_usuario.php?id=' . $_SESSION['usuario_id'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Alterar Senha — TechPortal</title>
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
    <h1 class="page-title">Alterar Senha
      <?php if ($ehAdmin && $idAlvo != $_SESSION['usuario_id']): ?>
        <small style="font-size:.6em;font-family:var(--ff-body);color:var(--accent)">
          — <?= htmlspecialchars($alvo['nome']) ?>
        </small>
      <?php endif; ?>
    </h1>

    <?php if ($erro): ?>
      <div class="alert alert-error"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>
    <?php if ($sucesso): ?>
      <div class="alert alert-success"><?= htmlspecialchars($sucesso) ?>
        &nbsp;<a href="<?= $voltarUrl ?>">← Voltar</a>
      </div>
    <?php endif; ?>

    <?php if (!$sucesso): ?>
    <div class="form-card">
      <form method="POST">
        <input type="hidden" name="id" value="<?= $idAlvo ?>">

        <?php if (!$ehAdmin): ?>
        <div class="form-group">
          <label class="form-label">Senha atual *</label>
          <input type="password" name="senha_atual" class="form-control"
                 placeholder="Digite sua senha atual" required>
        </div>
        <?php else: ?>
          <div class="alert alert-info" style="margin-bottom:1.2rem">
            Como administrador, você pode redefinir a senha sem precisar informar a senha atual.
          </div>
        <?php endif; ?>

        <div class="form-group">
          <label class="form-label">Nova senha *</label>
          <input type="password" name="nova_senha" class="form-control"
                 placeholder="Mínimo 6 caracteres" required>
        </div>
        <div class="form-group">
          <label class="form-label">Confirmar nova senha *</label>
          <input type="password" name="confirma" class="form-control"
                 placeholder="Repita a nova senha" required>
        </div>
        <div class="d-flex gap-2 mt-2">
          <button type="submit" class="btn btn-primary btn-lg">Salvar nova senha</button>
          <a href="<?= $voltarUrl ?>" class="btn btn-outline btn-lg">Cancelar</a>
        </div>
      </form>
    </div>
    <?php endif; ?>
  </div>
</div>

<footer class="site-footer">
  <div class="container">
    <p><strong>TechPortal</strong> — <a href="index.php">Portal</a></p>
  </div>
</footer>

</body>
</html>
