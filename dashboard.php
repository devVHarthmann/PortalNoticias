<?php
session_start();
require_once 'verifica_login.php';
require_once 'config.php';
require_once 'funcoes.php';
require_once 'classes/Noticia.php';
require_once 'classes/Usuario.php';

// Busca dados do usuário logado
$usuarioObj = new Usuario($db);
$usuario    = $usuarioObj->lerPorId($_SESSION['usuario_id']);
if (!$usuario) {
    session_destroy();
    header('Location: login.php');
    exit;
}

// Busca APENAS as notícias do usuário logado via query direta
$stmt = $db->prepare("SELECT * FROM noticias WHERE autor = ? ORDER BY data DESC");
$stmt->execute([$_SESSION['usuario_id']]);
$minhas = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalMinhas = count($minhas);

$sucesso = '';
if (!empty($_GET['ok']))  $sucesso = 'Notícia publicada com sucesso!';
if (!empty($_GET['del'])) $sucesso = 'Notícia excluída com sucesso.';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Painel — TechPortal</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="site-header">
  <div class="container">
    <div class="header-inner">
      <a href="index.php" class="site-logo">Tech<span>Portal</span><small>Tecnologia &amp; Inovação</small></a>
      <nav class="header-nav">
        <a href="index.php">Portal</a>
        <a href="nova_noticia.php" class="btn-nav-accent">+ Nova Notícia</a>
        <a href="logout.php">Sair</a>
      </nav>
    </div>
  </div>
</header>
<div class="strip"></div>

<div class="dashboard-header">
  <div class="container">
    <h1>Olá, <?= htmlspecialchars($usuario['nome']) ?> 👋</h1>
    <p>Bem-vindo ao seu painel de controle.</p>
  </div>
</div>

<div class="container">
  <div class="dashboard-grid">

    <!-- Sidebar -->
    <aside class="dashboard-sidebar">
      <div class="sidebar-card">
        <h3>Menu</h3>
        <nav class="sidebar-nav">
          <a href="dashboard.php" class="active">Dashboard</a>
          <a href="nova_noticia.php">Nova Notícia</a>
          <a href="editar_usuario.php?id=<?= $usuario['id'] ?>">Editar Perfil</a>
          <a href="usuarios.php">Todos os Usuários</a>
          <?php if (!empty($_SESSION["usuario_admin"])): ?>
          <a href="admin_noticias.php">Gerenciar Notícias</a>
          <?php endif; ?>
          <a href="index.php">Ver Portal</a>
          <a href="logout.php">Sair</a>
        </nav>
      </div>
      <div class="sidebar-card">
        <h3>Suas Estatísticas</h3>
        <div class="stat-block">
          <div class="stat-num"><?= $totalMinhas ?></div>
          <div class="stat-label">Notícias publicadas</div>
        </div>
      </div>
      <div class="sidebar-card">
        <h3>Conta</h3>
        <p style="font-size:.85rem;color:var(--text-muted);margin-bottom:.8rem;">
          <strong><?= htmlspecialchars($usuario['nome']) ?></strong><br>
          <?= htmlspecialchars($usuario['email']) ?>
        </p>
        <a href="editar_usuario.php?id=<?= $usuario['id'] ?>" class="btn btn-outline btn-sm w-100">Editar perfil</a>
      </div>
      <?php if (!empty($_SESSION['usuario_admin'])): ?>
      <div class="sidebar-card" style="border-color:var(--accent)">
        <h3 style="color:var(--accent)">⚙️ Administração</h3>
        <nav class="sidebar-nav">
          <a href="admin_noticias.php">Gerenciar Notícias</a>
          <a href="usuarios.php">Gerenciar Usuários</a>
        </nav>
      </div>
      <?php endif; ?>
    </aside>

    <!-- Conteúdo -->
    <div class="dashboard-content">

      <?php if ($sucesso): ?>
        <div class="alert alert-success"><?= htmlspecialchars($sucesso) ?></div>
      <?php endif; ?>

      <div class="d-flex justify-between align-center mb-2">
        <h2 class="page-title" style="border:none;margin:0;padding:0">Minhas Notícias</h2>
        <a href="nova_noticia.php" class="btn btn-accent">+ Nova Notícia</a>
      </div>

      <?php if (empty($minhas)): ?>
        <div class="alert alert-info">
          Você ainda não publicou nenhuma notícia.
          <a href="nova_noticia.php">Publique a primeira agora!</a>
        </div>
      <?php else: ?>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>Título</th>
                <th>Data</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($minhas as $n): ?>
                <tr>
                  <td>
                    <a href="noticia.php?id=<?= (int)$n['id'] ?>"><?= htmlspecialchars($n['titulo']) ?></a>
                  </td>
                  <td><?= date('d/m/Y', strtotime($n['data'])) ?></td>
                  <td>
                    <div class="d-flex gap-1">
                      <a href="editar_noticia.php?id=<?= (int)$n['id'] ?>" class="btn btn-outline btn-sm">Editar</a>
                      <a href="excluir_noticia.php?id=<?= (int)$n['id'] ?>"
                         class="btn btn-danger btn-sm"
                         data-confirm="Esta ação é permanente e não pode ser desfeita." data-confirm-title="Excluir notícia?" data-confirm-icon="🗑️" data-confirm-label="Sim, excluir">Excluir</a>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

  </div>
</div>

<footer class="site-footer">
  <div class="container">
    <p><strong>TechPortal</strong> — <a href="index.php">Voltar ao portal</a></p>
  </div>
</footer>

<script src="modal.js"></script>
</body>
</html>
