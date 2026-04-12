<?php
session_start();
require_once 'verifica_login.php';
require_once 'config.php';
require_once 'funcoes.php';
require_once 'classes/Usuario.php';
require_once 'classes/Noticia.php';

$ehAdmin    = !empty($_SESSION['usuario_admin']);
$usuarioObj = new Usuario($db);
$noticiaObj = new Noticia($db);

$stmtU    = $usuarioObj->ler();
$usuarios = $stmtU->fetchAll(PDO::FETCH_ASSOC);

$stmtN    = $noticiaObj->ler();
$noticias = $stmtN->fetchAll(PDO::FETCH_ASSOC);

$contagem = [];
foreach ($noticias as $n) {
    $contagem[$n['autor']] = ($contagem[$n['autor']] ?? 0) + 1;
}

$sucesso = '';
if (!empty($_GET['del'])) $sucesso = 'Usuário excluído com sucesso.';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Usuários — TechPortal</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="site-header">
  <div class="container">
    <div class="header-inner">
      <a href="index.php" class="site-logo">Tech<span>Portal</span><small>Tecnologia &amp; Inovação</small></a>
      <nav class="header-nav">
        <a href="dashboard.php">Painel</a>
        <?php if ($ehAdmin): ?>
          <a href="admin_noticias.php">Notícias</a>
        <?php endif; ?>
        <a href="index.php">Portal</a>
        <a href="logout.php">Sair</a>
      </nav>
    </div>
  </div>
</header>
<div class="strip"></div>

<div class="page-wrap">
  <div class="container">
    <h1 class="page-title">Usuários Cadastrados</h1>

    <?php if ($sucesso): ?>
      <div class="alert alert-success"><?= htmlspecialchars($sucesso) ?></div>
    <?php endif; ?>

    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Nome</th>
            <th>E-mail</th>
            <th>Telefone</th>
            <th>Perfil</th>
            <th>Notícias</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($usuarios as $u): ?>
            <tr>
              <td><?= (int)$u['id'] ?></td>
              <td>
                <?= htmlspecialchars($u['nome']) ?>
                <?php if ($u['id'] == $_SESSION['usuario_id']): ?>
                  <span class="badge badge-green">Você</span>
                <?php endif; ?>
              </td>
              <td><?= htmlspecialchars($u['email']) ?></td>
              <td><?= htmlspecialchars($u['telefone'] ?? '—') ?></td>
              <td>
                <?php if (!empty($u['is_admin'])): ?>
                  <span class="badge" style="background:#fef3c7;color:#92400e">Admin</span>
                <?php else: ?>
                  <span class="badge badge-gray">Usuário</span>
                <?php endif; ?>
              </td>
              <td>
                <span class="badge badge-gray"><?= $contagem[$u['id']] ?? 0 ?></span>
              </td>
              <td>
                <?php if ($ehAdmin || $u['id'] == $_SESSION['usuario_id']): ?>
                  <div class="d-flex gap-1 flex-wrap">
                    <a href="editar_usuario.php?id=<?= $u['id'] ?>" class="btn btn-outline btn-sm">Editar</a>
                    <a href="alterar_senha.php?id=<?= $u['id'] ?>" class="btn btn-outline btn-sm">Senha</a>
                    <?php if ($u['id'] != $_SESSION['usuario_id']): ?>
                      <a href="excluir_usuario.php?id=<?= $u['id'] ?>"
                         class="btn btn-danger btn-sm"
                         data-confirm="A conta de &quot;<?= htmlspecialchars($u['nome']) ?>&quot; e todas as suas notícias serão excluídas."
                         data-confirm-title="Excluir usuário?"
                         data-confirm-icon="⚠️"
                         data-confirm-label="Sim, excluir">Excluir</a>
                    <?php endif; ?>
                  </div>
                <?php else: ?>
                  <span class="text-muted">—</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

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
