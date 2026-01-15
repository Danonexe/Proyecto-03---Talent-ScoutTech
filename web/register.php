<?php
require_once dirname(__FILE__) . '/private/conf.php';

# Require logged users
# require dirname(__FILE__) . '/private/auth.php';

if (isset($_POST['username']) && isset($_POST['password'])) {
	# Just in from POST => save to database
    $username = $_POST['username'];
	$password = $_POST['password'];

    if (strlen($username) < 3 || strlen($password) < 6) {
        die("Username must be at least 3 chars and password at least 6 chars.");
    }
	
	$username = SQLite3::escapeString($username);
    // Hash password
	$password = password_hash($password, PASSWORD_DEFAULT);
    $password = SQLite3::escapeString($password); // Escape just in case, though hash is safe

	$query = "INSERT INTO users (username, password) VALUES ('$username', '$password')";

	$db->query($query) or die("Invalid query");
    header("Location: list_players.php");
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
            <h1>Register</h1>
        </header>
        <main class="player">
            <form action="#" method="post">
                <input type="hidden" name="id" value="<?=$id?>">
                <label>Username:</label>
                <input type="text" name="username">
                <label>Password:</label>
                <input type="password" name="password">
                <input type="submit" value="Send">
            </form>
                <form action="#" method="post" class="menu-form">
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

