<?php
session_start();
require_once 'config/config.php';
require_once 'funcoes.php';
require_once 'classes/Noticia.php';
require_once 'classes/Usuario.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
  header('Location: index.php');
  exit;
}

$noticiaObj = new Noticia($db);
$noticia    = $noticiaObj->lerPorId($id);
if (!$noticia) {
  header('Location: index.php');
  exit;
}

$usuarioObj = new Usuario($db);
$autor      = $usuarioObj->lerPorId($noticia['autor']);
$autorNome  = $autor ? $autor['nome'] : 'Desconhecido';
$ehAutor    = !empty($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $noticia['autor'];
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($noticia['titulo']) ?> — TechPortal</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>

  <header class="site-header">
    <div class="container">
      <div class="header-inner">
        <a href="index.php" class="site-logo">Tech<span>Portal</span><small>Tecnologia &amp; Inovação</small></a>
        <nav class="header-nav">
          <?php if (!empty($_SESSION['usuario_id'])): ?>
            <a href="dashboard.php">Painel</a>
            <a href="nova_noticia.php" class="btn-nav-accent">+ Nova Notícia</a>
            <a href="logout.php">Sair</a>
          <?php else: ?>
            <a href="login.php">Entrar</a>
            <a href="cadastrar.php" class="btn-nav-accent">Criar conta</a>
          <?php endif; ?>
        </nav>
      </div>
    </div>
  </header>
  <div class="strip"></div>

  <main>
    <div class="container--narrow">
      <div class="article-header">
        <div class="article-cat">Tecnologia</div>
        <h1 class="article-title"><?= htmlspecialchars($noticia['titulo']) ?></h1>
        <div class="article-byline">
          <span>Por <strong><?= htmlspecialchars($autorNome) ?></strong></span>
          <span>·</span>
          <span><?= date('d/m/Y', strtotime($noticia['data'])) ?></span>
        </div>
      </div>

      <?php if (!empty($noticia['imagem']) && file_exists('imagens/' . $noticia['imagem'])): ?>
        <img class="article-img" src="imagens/<?= htmlspecialchars($noticia['imagem']) ?>" alt="<?= htmlspecialchars($noticia['titulo']) ?>">
      <?php endif; ?>

      <div class="article-body">
        <?php
        $paragrafos = explode("\n", nl2br(htmlspecialchars($noticia['noticia'])));
        foreach ($paragrafos as $p) {
          $p = trim($p);
          if ($p) echo "<p>$p</p>";
        }
        ?>
      </div>

      <div class="article-actions">
        <a href="index.php" class="btn btn-outline">← Voltar</a>
        <?php if ($ehAutor): ?>
          <a href="editar_noticia.php?id=<?= (int)$noticia['id'] ?>" class="btn btn-primary">Editar notícia</a>
          <a href="excluir_noticia.php?id=<?= (int)$noticia['id'] ?>"
            class="btn btn-danger"
            data-confirm="Esta ação é permanente e não pode ser desfeita." data-confirm-title="Excluir notícia?" data-confirm-icon="🗑️" data-confirm-label="Sim, excluir">Excluir</a>
        <?php endif; ?>
      </div>
    </div>
  </main>

  <footer class="site-footer">
    <div class="container">
      <p><strong>TechPortal</strong> — <a href="index.php">Voltar à página inicial</a></p>
    </div>
  </footer>

  <script src="modal.js"></script>
</body>

</html>