<?php
session_start();
require_once 'verifica_login.php';
require_once 'config/config.php';
require_once 'funcoes.php';
require_once 'classes/Noticia.php';

$ehAdmin = !empty($_SESSION['usuario_admin']);
$id      = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
if (!$id) { header('Location: dashboard.php'); exit; }

$noticiaObj = new Noticia($db);
$noticia    = $noticiaObj->lerPorId($id);

// Só o autor ou admin pode editar
if (!$noticia || (!$ehAdmin && $noticia['autor'] != $_SESSION['usuario_id'])) {
    header('Location: dashboard.php'); exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo   = trim($_POST['titulo']  ?? '');
    $conteudo = trim($_POST['noticia'] ?? '');
    $data     = $_POST['data']         ?? date('Y-m-d');
    $imagem   = $noticia['imagem'];

    if (!$titulo || !$conteudo || !$data) {
        $erro = 'Preencha todos os campos obrigatórios.';
    } else {
        if (!empty($_FILES['imagem']['name'])) {
            $ext        = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
            $permitidos = ['jpg','jpeg','png','gif','webp'];
            if (!in_array($ext, $permitidos)) {
                $erro = 'Formato de imagem não permitido.';
            } elseif ($_FILES['imagem']['size'] > 5 * 1024 * 1024) {
                $erro = 'Imagem muito grande. Máximo 5MB.';
            } else {
                $nomeArquivo = uniqid('img_') . '.' . $ext;
                if (!is_dir('imagens')) mkdir('imagens', 0755, true);
                if (move_uploaded_file($_FILES['imagem']['tmp_name'], 'imagens/' . $nomeArquivo)) {
                    if ($imagem && file_exists('imagens/' . $imagem)) unlink('imagens/' . $imagem);
                    $imagem = $nomeArquivo;
                } else {
                    $erro = 'Falha ao salvar a imagem.';
                }
            }
        }

        if (!$erro) {
            $noticiaObj->atualizar($id, $titulo, $conteudo, $data, $noticia['autor'], $imagem);
            header('Location: noticia.php?id=' . $id . '&ok=1'); exit;
        }
    }

    $noticia['titulo']  = $titulo;
    $noticia['noticia'] = $conteudo;
    $noticia['data']    = $data;
}

$voltarUrl = $ehAdmin ? 'admin_noticias.php' : 'dashboard.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editar Notícia — TechPortal</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="site-header">
  <div class="container">
    <div class="header-inner">
      <a href="index.php" class="site-logo">Tech<span>Portal</span><small>Tecnologia &amp; Inovação</small></a>
      <nav class="header-nav">
        <?php if ($ehAdmin): ?>
          <a href="admin_noticias.php">Notícias (Admin)</a>
        <?php endif; ?>
        <a href="dashboard.php">Painel</a>
        <a href="logout.php">Sair</a>
      </nav>
    </div>
  </div>
</header>
<div class="strip"></div>

<div class="page-wrap">
  <div class="container--narrow">
    <h1 class="page-title">Editar Notícia
      <?php if ($ehAdmin && $noticia['autor'] != $_SESSION['usuario_id']): ?>
        <small style="font-size:.55em;font-family:var(--ff-body);color:var(--accent)">— modo admin</small>
      <?php endif; ?>
    </h1>

    <?php if ($ehAdmin && $noticia['autor'] != $_SESSION['usuario_id']): ?>
      <div class="alert alert-info">Você está editando a notícia de outro usuário como administrador.</div>
    <?php endif; ?>

    <?php if ($erro): ?>
      <div class="alert alert-error"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <div class="form-card">
      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $id ?>">
        <div class="form-group">
          <label class="form-label">Título *</label>
          <input type="text" name="titulo" class="form-control"
                 value="<?= htmlspecialchars($noticia['titulo']) ?>" required>
        </div>
        <div class="form-group">
          <label class="form-label">Data de Publicação *</label>
          <input type="date" name="data" class="form-control"
                 value="<?= htmlspecialchars($noticia['data']) ?>" required>
        </div>
        <div class="form-group">
          <label class="form-label">Nova imagem de capa</label>
          <?php if (!empty($noticia['imagem']) && file_exists('imagens/' . $noticia['imagem'])): ?>
            <div class="mb-2">
              <img src="imagens/<?= htmlspecialchars($noticia['imagem']) ?>"
                   alt="Imagem atual" style="max-height:120px;border-radius:4px;border:1px solid var(--border)">
              <p class="form-hint">Envie uma nova para substituir.</p>
            </div>
          <?php endif; ?>
          <input type="file" name="imagem" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp">
          <span class="form-hint">Opcional. Deixe em branco para manter a imagem atual.</span>
        </div>
        <div class="form-group">
          <label class="form-label">Conteúdo *</label>
          <textarea name="noticia" class="form-control" rows="12" required><?= htmlspecialchars($noticia['noticia']) ?></textarea>
        </div>
        <div class="d-flex gap-2 mt-2">
          <button type="submit" class="btn btn-accent btn-lg">Salvar Alterações</button>
          <a href="<?= $voltarUrl ?>" class="btn btn-outline btn-lg">Cancelar</a>
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
