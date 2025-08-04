<?php

namespace Views;

require_once __DIR__ . '/../Services/MovieService.php';

use Services\MovieService;

session_start();

if (!isset($_SESSION['movies'])) {

    $service = new MovieService();
    $service->getMovies();
}

$movies = $_SESSION['movies'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <a href="TestAddView.php"><button>Add Movie</button></a>

    <form method="POST" action="../Controllers//MovieController.php?action=sortMovies">
        <input type="hidden" name="sort" value="publication_date">
        <button type="submit">Sort By Date</button>
    </form>

    <form method="POST" action="../Controllers//MovieController.php?action=sortMovies">
        <input type="hidden" name="sort" value="likes">
        <button type="submit">Sort By Likes</button>
    </form>

    <form method="POST" action="../Controllers//MovieController.php?action=sortMovies">
        <input type="hidden" name="sort" value="hates">
        <button type="submit">Sort By Hates</button>
    </form>

    <a href="TestUserCreateView.php"><button>Sign Up</button></a>

    <a href="TestUserLogin.php"><button>Sign In</button></a>

    <a href="TestUserLogin.php"><button>Logout</button></a>

    <?php
    foreach ($movies as $movie) {

        $buttonDisable = "";

        if ($movie->user_name === $_SESSION['user']) {
            $movie->user_name = "You";
            $buttonDisable = "disabled";
        }
        echo "<li>" . $movie->__toString() . "</li>";

        echo <<<HTML
    <form method="POST" action="../Controllers/MovieController.php?action=addVote">
        <input type="hiddene" name="movieId" value="{$movie->id}">
        <input type="hidden" name="vote" value="like">
        <button type="submit" $buttonDisable>Like</button>
    </form>

    <form method="POST" action="../Controllers/MovieController.php?action=addVote&vote=hate">
        <input type="hidden" name="movieId" value="{$movie->id}">
        <button type="submit" $buttonDisable>Hate</button>
    </form>

    <form method="POST" action="../Controllers/MovieController.php?action=getMovies">
        <input type="hidden" name="user_name" value="{$movie->user_name}">
        <button type="submit" $buttonDisable>$movie->user_name</button>
    </form>
    HTML;
        echo "<br>";
    }

    unset($_SESSION["movies"]);

    ?>
</body>

</html>