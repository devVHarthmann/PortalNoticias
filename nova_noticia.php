<?php
session_start();
require_once 'verifica_login.php';
require_once 'config/config.php';
require_once 'funcoes.php';
require_once 'classes/Noticia.php';

$erro   = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo  = trim($_POST['titulo']  ?? '');
    $noticia = trim($_POST['noticia'] ?? '');
    $data    = $_POST['data'] ?? date('Y-m-d');
    $autor   = (int)$_SESSION['usuario_id'];
    $imagem  = null;

    if (!$titulo || !$noticia || !$data) {
        $erro = 'Preencha todos os campos obrigatórios.';
    } else {
        // Upload de imagem
        if (!empty($_FILES['imagem']['name'])) {
            $ext       = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
            $permitidos = ['jpg','jpeg','png','gif','webp'];
            if (!in_array($ext, $permitidos)) {
                $erro = 'Formato de imagem não permitido. Use JPG, PNG, GIF ou WEBP.';
            } elseif ($_FILES['imagem']['size'] > 5 * 1024 * 1024) {
                $erro = 'Imagem muito grande. Máximo 5MB.';
            } else {
                $nomeArquivo = uniqid('img_') . '.' . $ext;
                if (!is_dir('imagens')) mkdir('imagens', 0755, true);
                if (move_uploaded_file($_FILES['imagem']['tmp_name'], 'imagens/' . $nomeArquivo)) {
                    $imagem = $nomeArquivo;
                } else {
                    $erro = 'Falha ao salvar a imagem.';
                }
            }
        }

        if (!$erro) {
            $noticiaObj = new Noticia($db);
            $noticiaObj->registrar($titulo, $noticia, $data, $autor, $imagem);
            header('Location: dashboard.php?ok=1'); exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nova Notícia — TechPortal</title>
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
    <h1 class="page-title">Nova Notícia</h1>

    <?php if ($erro): ?>
      <div class="alert alert-error"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <div class="form-card">
      <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
          <label class="form-label">Título *</label>
          <input type="text" name="titulo" class="form-control"
                 value="<?= htmlspecialchars($_POST['titulo'] ?? '') ?>"
                 placeholder="Título da notícia" required>
        </div>
        <div class="form-group">
          <label class="form-label">Data de Publicação *</label>
          <input type="date" name="data" class="form-control"
                 value="<?= htmlspecialchars($_POST['data'] ?? date('Y-m-d')) ?>" required>
        </div>
        <div class="form-group">
          <label class="form-label">Imagem de capa</label>
          <input type="file" name="imagem" class="form-control"
                 accept="image/jpeg,image/png,image/gif,image/webp">
          <span class="form-hint">Opcional. JPG, PNG, GIF ou WEBP. Máx. 5MB.</span>
        </div>
        <div class="form-group">
          <label class="form-label">Conteúdo da Notícia *</label>
          <textarea name="noticia" class="form-control" rows="12"
                    placeholder="Escreva o conteúdo completo da notícia..." required><?= htmlspecialchars($_POST['noticia'] ?? '') ?></textarea>
        </div>
        <div class="d-flex gap-2 mt-2">
          <button type="submit" class="btn btn-accent btn-lg">Publicar Notícia</button>
          <a href="dashboard.php" class="btn btn-outline btn-lg">Cancelar</a>
        </div>
      </form>
    </div>
  </div>
</div>

<footer class="site-footer">
  <div class="container">
    <p><strong>TechPortal</strong> — <a href="index.php">Portal</a></p>
  </div>
</footer>

</body>
</html>
