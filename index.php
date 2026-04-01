<?php
include_once "./classes/Usuario.php";
include_once "./classes/Noticia.php";
include_once "./config/config.php";

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="CSS/index.css">
</head>

<body>
    <header class="hd1">
        <div class="logoContainer">

        </div>
        <nav class="nv1">

        </nav>
    </header>
    <main class="mn1">
        <div class="noticiasContainerSuperior">
            
            <div class="containerNoticias">

                <?php
                $news = new Noticia($db);
                $user = new Usuario($db);
                $stmt = $news->ler();
                ?>
                <?php while ($noticia = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <?php
                    $nome = $user->lerPorId($noticia["autor"])["nome"];
                    ?>
                    <div class="noticia">
                        <h1><?php echo $noticia["titulo"] ?></h1>
                        <hr>
                        <p><?php echo $noticia["noticia"] ?></p>
                        <p><small><?php echo $nome ?></small></p>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

    </main>
</body>

</html>