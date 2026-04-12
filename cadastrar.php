<?php
session_start();
require_once 'config/config.php';
require_once 'classes/Usuario.php';

if (!empty($_SESSION['usuario_id'])) {
    header('Location: dashboard.php'); exit;
}

$erro  = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome     = trim($_POST['nome']     ?? '');
    $email    = trim($_POST['email']    ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $senha    = $_POST['senha']         ?? '';
    $confirma = $_POST['confirma']      ?? '';

    if (!$nome || !$email || !$senha) {
        $erro = 'Preencha todos os campos obrigatórios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'E-mail inválido.';
    } elseif (strlen($senha) < 6) {
        $erro = 'A senha deve ter pelo menos 6 caracteres.';
    } elseif ($senha !== $confirma) {
        $erro = 'As senhas não coincidem.';
    } else {
        $usuarioObj = new Usuario($db);
        try {
            $usuarioObj->registrar($nome, $email, $telefone, $senha);
            $sucesso = 'Conta criada com sucesso! Faça login para continuar.';
        } catch (PDOException $e) {
            if (str_contains($e->getMessage(), 'Duplicate')) {
                $erro = 'Este e-mail já está cadastrado.';
            } else {
                $erro = 'Erro ao criar conta. Tente novamente.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Criar Conta — TechPortal</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">

  <div class="auth-logo"><a href="index.php">Tech<span>Portal</span></a></div>

  <div class="auth-box">
    <div class="form-card">
      <h1 class="form-title">Criar conta</h1>
      <p class="form-subtitle">Junte-se ao portal e comece a publicar notícias.</p>

      <?php if ($erro): ?>
        <div class="alert alert-error"><?= htmlspecialchars($erro) ?></div>
      <?php endif; ?>
      <?php if ($sucesso): ?>
        <div class="alert alert-success"><?= htmlspecialchars($sucesso) ?>
          <br><a href="login.php">Ir para o login →</a>
        </div>
      <?php endif; ?>

      <?php if (!$sucesso): ?>
      <form method="POST">
        <div class="form-group">
          <label class="form-label">Nome completo *</label>
          <input type="text" name="nome" class="form-control"
                 value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>"
                 placeholder="Seu nome" required>
        </div>
        <div class="form-group">
          <label class="form-label">E-mail *</label>
          <input type="email" name="email" class="form-control"
                 value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                 placeholder="seu@email.com" required>
        </div>
        <div class="form-group">
          <label class="form-label">Telefone</label>
          <input type="text" name="telefone" class="form-control"
                 value="<?= htmlspecialchars($_POST['telefone'] ?? '') ?>"
                 placeholder="(00) 00000-0000">
        </div>
        <div class="form-group">
          <label class="form-label">Senha *</label>
          <input type="password" name="senha" class="form-control"
                 placeholder="Mínimo 6 caracteres" required>
        </div>
        <div class="form-group">
          <label class="form-label">Confirmar senha *</label>
          <input type="password" name="confirma" class="form-control"
                 placeholder="Repita a senha" required>
        </div>
        <div class="mt-2">
          <button type="submit" class="btn btn-primary btn-block btn-lg">Criar conta</button>
        </div>
      </form>
      <?php endif; ?>
    </div>
    <p class="auth-footer">Já tem conta? <a href="login.php">Entrar</a></p>
    <p class="auth-footer"><a href="index.php">← Voltar ao portal</a></p>
  </div>

</body>
</html>
