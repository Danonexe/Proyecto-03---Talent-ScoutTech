<?php
session_start();
# On logout
if (isset($_POST['Logout'])) {
    # Delete session
    session_destroy();
    unset($_SESSION['user']);
    unset($_SESSION['userId']);

    header("Location: index.php");
}
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="css/style.css">
    <title>Práctica RA3</title>
</head>

<body>
    <header>
        <h1>Developers Awards</h1>
    </header>
    <main>
        <h2><a href="insert_player.php"> Add a new player</a></h2>
        <h2><a href="list_players.php"> List of players</a></h2>
        <h2><a href="buscador.html"> Search a player</a></h2>

    </main>
    <form action="#" method="post" class="menu-form">
        <input type="submit" name="Logout" value="Logout" class="logout">
    </form>
    <footer>
        <h4>Puesta en producción segura</h4>
        < Please <a href="http://www.donate.co?amount=100&amp;destination=ACMEScouting/"> donate</a> >
    </footer>
</body>

</html>