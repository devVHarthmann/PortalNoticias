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
    <title>TechNews</title>
    <link rel="stylesheet" href="CSS/index.css">
    <script src="https://cdn.tailwindcss.com"></script>

</head>

<body>
    <main class="mn1">
        <aside id="sidebar">
            <div id="logo"></div>
            <button class="btn"><a href="login.php">Login</a></button>
            <button class="btn"><a  href="cadastrar.php">Cadastrar</a></button>
        </aside>

        <div class="contMain">
            <span id="tit1">Notícias</span>
            <hr id="hrT">
            <div class="containerNoticias">

                <?php
                $news = new Noticia($db);
                $user = new Usuario($db);
                $stmt = $news->ler();
                ?>
                <?php while ($noticia = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <?php
                    $nome = $user->lerPorId($noticia["autor"])["nome"];
                    $data = new DateTime($noticia["data"]);
                    $dataFormatada = $data->format("d/m H:i");
                    ?>
                    <div id="noticia" class="bg-neutral-300 shrink-0 w-80 h-52 max-h-52 p-4 shadow-sm rounded-lg  hover:shadow-lg transition-shadow duration-500 ">
                        <h1 class="text-3xl"><?php echo $noticia["titulo"] ?></h1>
                        <hr class="border-t border-black mt-1">
                        <p class="min-h-24"><?php echo $noticia["noticia"] ?></p>
                        <p class="mt-4 text-right"><strong><small><?php echo $nome . ", " . $dataFormatada ?></small></strong></p>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>


    </main>
</body>

</html>