<?php
session_start();
require_once 'verifica_login.php';
require_once 'config/config.php';
require_once 'funcoes.php';
require_once 'classes/Noticia.php';
require_once 'classes/Usuario.php';

// Apenas admin
if (empty($_SESSION['usuario_admin'])) {
    header('Location: dashboard.php'); exit;
}

$noticiaObj = new Noticia($db);
$usuarioObj = new Usuario($db);

$stmt     = $noticiaObj->ler();
$noticias = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Enriquece com nome do autor
foreach ($noticias as &$n) {
    $a = $usuarioObj->lerPorId($n['autor']);
    $n['autor_nome'] = $a ? $a['nome'] : 'Desconhecido';
}
unset($n);

$sucesso = '';
if (!empty($_GET['del'])) $sucesso = 'Notícia excluída com sucesso.';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin — Notícias — TechPortal</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="site-header">
  <div class="container">
    <div class="header-inner">
      <a href="index.php" class="site-logo">Tech<span>Portal</span><small>Tecnologia &amp; Inovação</small></a>
      <nav class="header-nav">
        <a href="dashboard.php">Painel</a>
        <a href="usuarios.php">Usuários</a>
        <a href="nova_noticia.php" class="btn-nav-accent">+ Nova</a>
        <a href="logout.php">Sair</a>
      </nav>
    </div>
  </div>
</header>
<div class="strip"></div>

<div class="page-wrap">
  <div class="container">

    <div class="d-flex justify-between align-center mb-2 flex-wrap gap-2">
      <h1 class="page-title" style="border:none;margin:0;padding:0">
        Todas as Notícias
        <span class="badge badge-gray" style="font-size:.6em;vertical-align:middle"><?= count($noticias) ?></span>
      </h1>
      <a href="nova_noticia.php" class="btn btn-accent">+ Nova Notícia</a>
    </div>

    <?php if ($sucesso): ?>
      <div class="alert alert-success"><?= htmlspecialchars($sucesso) ?></div>
    <?php endif; ?>

    <?php if (empty($noticias)): ?>
      <div class="alert alert-info">Nenhuma notícia cadastrada.</div>
    <?php else: ?>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Título</th>
              <th>Autor</th>
              <th>Data</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($noticias as $n): ?>
              <tr>
                <td><?= (int)$n['id'] ?></td>
                <td><a href="noticia.php?id=<?= (int)$n['id'] ?>"><?= htmlspecialchars($n['titulo']) ?></a></td>
                <td><?= htmlspecialchars($n['autor_nome']) ?></td>
                <td><?= date('d/m/Y', strtotime($n['data'])) ?></td>
                <td>
                  <div class="d-flex gap-1">
                    <a href="editar_noticia.php?id=<?= (int)$n['id'] ?>" class="btn btn-outline btn-sm">Editar</a>
                    <a href="excluir_noticia.php?id=<?= (int)$n['id'] ?>"
                       class="btn btn-danger btn-sm"
                       data-confirm="A notícia &quot;<?= htmlspecialchars($n['titulo']) ?>&quot; será excluída permanentemente."
                       data-confirm-title="Excluir notícia?"
                       data-confirm-icon="🗑️"
                       data-confirm-label="Sim, excluir">Excluir</a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>

    <div class="mt-3">
      <a href="dashboard.php" class="btn btn-outline">← Voltar ao Painel</a>
    </div>
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
