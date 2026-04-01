<?php
include_once "./classes/usuario.php";
include_once "./config/config.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <form action="" method="post">
        <input type="text" name="nome">
        <input type="email" name="email">
        <input type="text" name="telefone">
        <input type="password" name="senha">
        <button type="submit">VAI</button>

    </form>
    <form action="" method="post">
        <input type="number" name="id">
        <button type="submit" name="delete">SAI</button>
        <button type="submit" name="ler">LÊ</button>


    </form>
    <form action="" method="post">
        <input type="number" name="id1">
        <input type="text" name="nome1">
        <input type="email" name="email1">
        <input type="text" name="telefone1">

        <button type="submit" name="atualizar">ATT</button>
    </form>
    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        if (isset($_POST['nome'])) {
            $user = new Usuario($db);
            $nome = $_POST['nome'];
            $email = $_POST['email'];
            $telefone = $_POST['telefone'];
            $senha = $_POST['senha'];
            $user->registrar($nome, $email, $telefone, $senha);
            var_dump($user->ler());
        }

        if (isset($_POST['delete'])) {
            $id = $_POST['id'];
            $user = new Usuario($db);
            $user->deletar($id);
        }
        if (isset($_POST['ler'])) {
            $id = $_POST['id'];
            $usuarioObj = new Usuario($db);
            $user = $usuarioObj->lerPorId($id);

            if ($user) {
                echo "<h2>Dados do Usuário</h2>";
                echo "ID: " . $user['id'] . "<br>";
                echo "Nome: " . $user['nome'] . "<br>";
                echo "Email: " . $user['email'] . "<br>";
            } else {
                echo "Usuário não encontrado.";
            }
        }
        if (isset($_POST["atualizar"])) {
            $user = new Usuario($db);
            $id = $_POST['id1'];
            $nome = $_POST['nome1'];
            $email = $_POST['email1'];
            $telefone = $_POST['telefone1'];

            $user->atualizar($id, $nome, $email, $telefone);

        }
    }

    ?>
</body>

</html>