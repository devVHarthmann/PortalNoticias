<?php
session_start();
require_once 'verifica_login.php';
require_once 'config/config.php';
require_once 'classes/Usuario.php';

$ehAdmin = !empty($_SESSION['usuario_admin']);
$id      = (int)($_GET['id'] ?? 0);

// Apenas o próprio usuário ou admin pode excluir
if (!$id || (!$ehAdmin && $id != $_SESSION['usuario_id'])) {
    header('Location: dashboard.php'); exit;
}

$usuarioObj = new Usuario($db);
$usuarioObj->deletar($id);

// Se excluiu a própria conta, encerra sessão
if ($id == $_SESSION['usuario_id']) {
    session_destroy();
    header('Location: index.php?bye=1');
} else {
    header('Location: usuarios.php?del=1');
}
exit;
