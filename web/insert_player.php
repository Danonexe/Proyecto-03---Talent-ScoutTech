<?php
require_once dirname(__FILE__) . '/private/conf.php';

# Require logged users
require dirname(__FILE__) . '/private/auth.php';

if (isset($_POST['name']) && isset($_POST['team'])) {
    # Just in from POST => save to database
    $name = $_POST['name'];
    $team = $_POST['team'];

    $name = SQLite3::escapeString($name);
    $team = SQLite3::escapeString($team);

    // Modify player or add a new one
    if (isset($_GET['id']))
        $query = "INSERT OR REPLACE INTO players (playerid, name, team) VALUES ('" . $_GET['id'] . "','$name', '$team')";
    else
        $query = "INSERT INTO players (name, team) VALUES ('$name', '$team')";

    $db->query($query) or die("Invalid query");
} else {
    # Show info to modify
    if (isset($_GET['id'])) {
        # Edit from database
        $id = $_GET['id'];
        $id = SQLite3::escapeString($id); // Secure ID too

        $query = "SELECT name, team FROM players WHERE playerid = '$id'";

        $result = $db->query($query) or die("$query");
        $row = $result->fetchArray() or die("modifying a nonexistent player!");

        $name = $row['name'];
        $team = $row['team'];
    }
}

# Show form

?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="css/style.css">
    <title>Práctica RA3 - Players list</title>
</head>

<body>
    <header>
        <h1>Player</h1>
    </header>
    <main class="player">
        <form action="#" method="post">
            <input type="hidden" name="id" value="<?= $id ?>"><br>
            <h3>Player name</h3>
            <textarea name="name"><?= htmlspecialchars($name) ?></textarea><br>
            <h3>Team name</h3>
            <textarea name="team"><?= htmlspecialchars($team) ?></textarea><br>
            <input type="submit" value="Send">
        </form>
        <form action="#" method="post" class="menu-form">
            <a href="index.php">Back to home</a>
            <a href="list_players.php">Back to list</a>
            <input type="submit" name="Logout" value="Logout" class="logout">
        </form>
    </main>
    <footer class="listado">
        <img src="images/logo-iesra-cadiz-color-blanco.png">
        <h4>Puesta en producción segura</h4>
        < Please <a href="http://www.donate.co?amount=100&amp;destination=ACMEScouting/"> donate</a> >
    </footer>
</body>

</html>