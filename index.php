<?php
session_start();
require_once 'config/config.php';
require_once 'funcoes.php';
require_once 'classes/Noticia.php';
require_once 'classes/Usuario.php';

// ========== CLIMA ==========
define('OWM_API_KEY', 'e355c23ba8adfc8a397cfe9f8a9b71fb');

$cidade      = 'sapucaia';
$temperatura = null;
$condicao    = null;

$cache_key = 'weather_' . md5($cidade);

$cache_valido = isset($_SESSION[$cache_key])
    && (time() - $_SESSION[$cache_key]['timestamp']) < 1800;

if ($cache_valido) {
    $temperatura = $_SESSION[$cache_key]['temperatura'];
    $condicao    = $_SESSION[$cache_key]['condicao'];
} else {
    $url = sprintf(
        'https://api.openweathermap.org/data/2.5/weather?q=%s&appid=%s&units=metric&lang=pt_br',
        urlencode($cidade),
        OWM_API_KEY
    );

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 5,
        CURLOPT_FAILONERROR    => true,
    ]);

    $response = curl_exec($ch);
    $erro     = curl_errno($ch);
    curl_close($ch);

    if (!$erro && $response !== false) {
        $data = json_decode($response, true);

        if (isset($data['main']['temp'], $data['weather'][0]['main'])) {
            $temperatura = round($data['main']['temp']);
            $condicao    = $data['weather'][0]['main'];

            $_SESSION[$cache_key] = [
                'temperatura' => $temperatura,
                'condicao'    => $condicao,
                'timestamp'   => time(),
            ];
        }
    }
}
// ===========================

$noticiaObj = new Noticia($db);
$stmt       = $noticiaObj->ler();
$noticias   = $stmt->fetchAll(PDO::FETCH_ASSOC);

$usuarioObj = new Usuario($db);
foreach ($noticias as &$n) {
    $autor = $usuarioObj->lerPorId($n['autor']);
    $n['autor_nome'] = $autor ? $autor['nome'] : 'Desconhecido';
}
unset($n);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TechPortal — Tecnologia &amp; Inovação</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="site-header">
  <div class="container">
    <div class="header-inner">
      <a href="index.php" class="site-logo">
        Tech<span>Portal</span>
        <small>Tecnologia &amp; Inovação</small>
      </a>
      <div class="header-right">
        <nav class="header-nav">
        <?php if (!empty($_SESSION['usuario_id'])): ?>
          <a href="dashboard.php"><span>Painel</span></a>
          <a href="logout.php">Sair</a>
        <?php else: ?>
          <a href="login.php">Entrar</a>
          <a href="cadastrar.php" class="btn-nav-accent">Criar conta</a>
        <?php endif; ?>
      </nav>
      </div>
    </div>
  </div>
</header>
<div class="strip"></div>

<section class="hero">
  <div class="container">
    <div class="hero-content">
      <?php if ($temperatura !== null): ?>
        <div class="weather-widget weather-widget--hero">
          <span class="weather-temp"><?= $temperatura ?>°C</span>
          <span class="weather-city">Sapucaia do Sul</span>
        </div>
      <?php endif; ?>
      <h1>Tecnologia &amp; Inovação<br>em Primeiro Lugar</h1>
      <p>As últimas notícias sobre tecnologia, startups, inteligência artificial e o futuro digital.</p>
      <?php if (empty($_SESSION['usuario_id'])): ?>
        <a href="cadastrar.php" class="btn btn-accent btn-lg">Criar conta grátis</a>
      <?php else: ?>
        <a href="nova_noticia.php" class="btn btn-accent btn-lg">+ Publicar Notícia</a>
      <?php endif; ?>
    </div>
  </div>
</section>

<main class="section">
  <div class="container">
    <div class="section-header">
      <h2 class="section-title">Últimas Notícias</h2>
      <span class="text-muted"><?= count($noticias) ?> publicações</span>
    </div>

    <?php if (empty($noticias)): ?>
      <div class="alert alert-info">
        Nenhuma notícia publicada ainda.
        <?php if (!empty($_SESSION['usuario_id'])): ?>
          <a href="nova_noticia.php">Seja o primeiro a publicar!</a>
        <?php else: ?>
          <a href="cadastrar.php">Cadastre-se</a> e publique a primeira notícia.
        <?php endif; ?>
      </div>
    <?php else: ?>
      <div class="news-grid">
        <?php foreach ($noticias as $noticia): ?>
          <article class="card">
            <?php if (!empty($noticia['imagem']) && file_exists('imagens/' . $noticia['imagem'])): ?>
              <img class="card-img" src="imagens/<?= htmlspecialchars($noticia['imagem']) ?>" alt="<?= htmlspecialchars($noticia['titulo']) ?>">
            <?php else: ?>
              <div class="card-img-placeholder">📰</div>
            <?php endif; ?>
            <div class="card-body">
              <span class="card-category">Tecnologia</span>
              <h3 class="card-title">
                <a href="noticia.php?id=<?= (int)$noticia['id'] ?>"><?= htmlspecialchars($noticia['titulo']) ?></a>
              </h3>
              <p class="card-excerpt"><?php
                $txt = strip_tags($noticia['noticia']);
                echo htmlspecialchars(mb_strlen($txt) > 50 ? mb_substr($txt,0,50).'…' : $txt);
              ?></p>
              <div class="card-meta">
                <span class="meta-author"><?= htmlspecialchars($noticia['autor_nome']) ?></span>
                <span class="dot">·</span>
                <span><?= date('d/m/Y', strtotime($noticia['data'])) ?></span>
              </div>
            </div>
            <div class="card-actions">
              <a href="noticia.php?id=<?= (int)$noticia['id'] ?>" class="btn btn-primary btn-sm">Ler mais →</a>
              <?php if (!empty($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $noticia['autor']): ?>
                <a href="editar_noticia.php?id=<?= (int)$noticia['id'] ?>" class="btn btn-outline btn-sm">Editar</a>
              <?php endif; ?>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</main>

<footer class="site-footer">
  <div class="container">
    <p><strong>TechPortal</strong> — Tecnologia &amp; Inovação &nbsp;|&nbsp;
       Desenvolvido com PHP &amp; MySQL &nbsp;&nbsp;
       <a href="login.php">Login</a> &nbsp;·&nbsp; <a href="cadastrar.php">Cadastro</a>
    </p>
  </div>
</footer>

</body>
</html>
