<!doctype html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="css/style.css">
    <title>Práctica RA3 - Comments editor</title>
</head>

<body>
    <header>
        <h1>Comments editor</h1>
    </header>
    <main class="player">

        <?php
        require_once dirname(__FILE__) . '/private/conf.php';

        # Require logged users
        require dirname(__FILE__) . '/private/auth.php';

        # List comments
        if (isset($_GET['id'])) {
            $query = "SELECT commentId, username, body FROM comments C, users U WHERE C.playerId =" . $_GET['id'] . " AND U.userId = C.userId order by C.playerId desc";

            $result = $db->query($query) or die("Invalid query: " . $query);

            while ($row = $result->fetchArray()) {
                echo "<div>
                <h4> " . htmlspecialchars($row['username']) . "</h4> 
                <p>commented: " . htmlspecialchars($row['body']) . "</p>
              </div>";
            }

            $playerId = $_GET['id'];
        }

        # Show form
        
        ?>

        <div>
            <a href="list_players.php">Back to list</a>
            <a class="black" href="add_comment.php?id=<?php echo $playerId; ?>"> Add comment</a>
        </div>

    </main>
    <footer class="listado">
        <img src="images/logo-iesra-cadiz-color-blanco.png">
        <h4>Puesta en producción segura</h4>
        < Please <a href="http://www.donate.co?amount=100&amp;destination=ACMEScouting/"> donate</a> >
    </footer>
</body>

</html>