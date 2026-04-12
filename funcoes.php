<?php
/**
 * Retorna um trecho (resumo) do texto da notícia.
 */
function resumo(string $texto, int $limite = 200): string
{
    $texto = strip_tags($texto);
    if (mb_strlen($texto) <= $limite) {
        return $texto;
    }
    return mb_substr($texto, 0, $limite) . '…';
}

/**
 * Formata uma data do banco (YYYY-MM-DD) para exibição em pt-BR.
 */
function formatarData(string $data): string
{
    $ts = strtotime($data);
    if ($ts === false) return $data;
    return date('d/m/Y', $ts);
}

/**
 * Redireciona para uma URL e encerra a execução.
 */
function redirecionar(string $url): void
{
    header("Location: $url");
    exit;
}

/**
 * Sanitiza uma string para exibição segura no HTML.
 */
function h(string $str): string
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

/**
 * Verifica se o usuário está logado e redireciona caso não esteja.
 * Deve ser chamada após session_start().
 */
function exigirLogin(string $destino = 'login.php'): void
{
    if (empty($_SESSION['usuario_id'])) {
        redirecionar($destino);
    }
}

/**
 * Retorna true se o usuário está logado.
 */
function estaLogado(): bool
{
    return !empty($_SESSION['usuario_id']);
}
