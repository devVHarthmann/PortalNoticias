<?php
session_start();
include_once './config/config.php';
include_once './classes/Usuario.php';


if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

$usuario = new Usuario($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'])) {
        // Processar login
        $email = $_POST['email'];
        $senha = $_POST['senha'];
        if ($dados_usuario = $usuario->login($email, $senha)) {
            $_SESSION['usuario_id'] = $dados_usuario['id'];
            header('Location: index.php');
            exit();
        } else {
            $mensagem_erro = "Credenciais inválidas!";
            
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="CSS/login.css">
</head>

<body>
    <main>
        <div class="sideSolid"></div>

        <div class="formContainer">
            <form class="formLogin" action="" method="post">
                <h1>Login</h1>
                <label for="email">Email</label>
                <input type="email" name="email" id="email">
                <label for="senha">Senha</label>
                <input type="password" name="senha" id="senha">
                <button class="submitLog" type="submit" name="login">Entrar</button>
                 
            </form>
            
        </div>
    </main>
    <footer>

    </footer>
</body>

</html>