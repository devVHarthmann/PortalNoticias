<?php
session_start();
require_once 'config.php';
require_once 'classes/Usuario.php';

if (!empty($_SESSION['usuario_id'])) {
    header('Location: dashboard.php'); exit;
}

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    if (!$email || !$senha) {
        $erro = 'Preencha e-mail e senha.';
    } else {
        $usuarioObj = new Usuario($db);
        $usuario    = $usuarioObj->login($email, $senha);
        if ($usuario) {
            $_SESSION['usuario_id']    = $usuario['id'];
            $_SESSION['usuario_nome']  = $usuario['nome'];
            $_SESSION['usuario_admin'] = (int)($usuario['is_admin'] ?? 0);
            header('Location: dashboard.php'); exit;
        } else {
            $erro = 'E-mail ou senha incorretos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login — TechPortal</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">

  <div class="auth-logo"><a href="index.php">Tech<span>Portal</span></a></div>

  <div class="auth-box">
    <div class="form-card">
      <h1 class="form-title">Entrar</h1>
      <p class="form-subtitle">Acesse sua conta para publicar notícias.</p>

      <?php if ($erro): ?>
        <div class="alert alert-error"><?= htmlspecialchars($erro) ?></div>
      <?php endif; ?>

      <form method="POST">
        <div class="form-group">
          <label class="form-label">E-mail</label>
          <input type="email" name="email" class="form-control"
                 value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                 placeholder="seu@email.com" required>
        </div>
        <div class="form-group">
          <label class="form-label">Senha</label>
          <input type="password" name="senha" class="form-control"
                 placeholder="••••••••" required>
        </div>
        <div class="mt-2">
          <button type="submit" class="btn btn-primary btn-block btn-lg">Entrar</button>
        </div>
      </form>
    </div>
    <p class="auth-footer">Não tem conta? <a href="cadastro.php">Cadastre-se grátis</a></p>
    <p class="auth-footer"><a href="index.php">← Voltar ao portal</a></p>
  </div>

</body>
</html>
