<?php
session_start();
require_once 'verifica_login.php';
require_once 'config.php';
require_once 'classes/Noticia.php';

$ehAdmin = !empty($_SESSION['usuario_admin']);
$id      = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: dashboard.php'); exit; }

$noticiaObj = new Noticia($db);
$noticia    = $noticiaObj->lerPorId($id);

// Só o autor ou admin pode excluir
if (!$noticia || (!$ehAdmin && $noticia['autor'] != $_SESSION['usuario_id'])) {
    header('Location: dashboard.php'); exit;
}

if (!empty($noticia['imagem']) && file_exists('imagens/' . $noticia['imagem'])) {
    unlink('imagens/' . $noticia['imagem']);
}

$noticiaObj->deletar($id);

$destino = $ehAdmin ? 'admin_noticias.php?del=1' : 'dashboard.php?del=1';
header('Location: ' . $destino);
exit;
