<?php

namespace Views;

require_once __DIR__ . "/../Services/MovieService.php";
require_once __DIR__ . "/../Controllers/MovieController.php";

use Services\MovieService;
use Controllers\MovieController;

session_start();

//region Error Messages

// Check for any session error_messages and display them if they exist
if (isset($_SESSION['connectionError'])) {

    echo $_SESSION['connectionError'];
    unset($_SESSION['connectionError']);
}

if (isset($_SESSION['empty_data'])) {

    echo $_SESSION['empty_data'];
    unset($_SESSION['empty_data']);
}

if (isset($_SESSION['movie_exists'])) {

    echo $_SESSION['movie_exists'];
    unset($_SESSION['movie_exists']);

}
//endregion

//region DisplayMovies

/**
 * Displays a list of movies with their details, including title, publication date,
 * description, likes, hates, and the username of the poster. Also provides forms
 * to submit "Like" or "Hate" votes for each movie.
 *
 */
function displayMovies()
{

    $controller = new MovieController();

    // Get the current logged-in user's name from the session, or set to empty string if not set
    $username = $_SESSION["user"] ?? null;

    // Initialize variables for styling and button states
    $hiddenStyle = (!isset($_SESSION['user'])) ? "display: none;" : "";

    $hiddenLikeStyle = "";
    $buttonLikeDisable = "";

    $hiddenHateStyle = "";
    $buttonHateDisable = "";

    // Loop through each movie and display its details
    foreach ($_SESSION['movies'] as $movie) {

        // Initialize variable to hold curreny movie's username
        $displayUserName = $movie['user_name'] ?? '';

        // Check if the current movie's username is the same as the logged-in user
        if ($username === $movie['user_name']) {
            $displayUserName = "You" ?? '';
            $hiddenStyle = "display: none;";
        }

        // Check if the user has already liked or hated the movie
        if (isset($_SESSION['user']) && $displayUserName !== "You") {

            $isLike = $controller->checkVotes($movie['id']);

            if ($isLike === true) {

                $buttonLikeDisable = 'disabled';
                $hiddenLikeStyle = 'background-color: lightgray;';
            }

            if ($isLike === false) {

                $buttonHateDisable = 'disabled';
                $hiddenHateStyle = 'background-color: lightgray;';
            }
        }


        // Output the movie information using heredoc syntax
        echo <<<HTML
            <div class="movieInfo">
                <div class="topSection">
                    <h1>{$movie['title']}</h1>
                    <p>Posted: {$movie['publication_date']}</p>
                </div>

                <div class="middleSection">
                    <p>{$movie['description']}</p>
                </div>

                <div class="bottomSection">

                    <div class="votes">
                        <p>{$movie['likes']}</p>
                        <p id="seperator">|</p>
                        <p>{$movie['hates']}</p>
                    </div>

                    <div class="buttons">
                        <!-- Like button form -->
                        <form method="POST" action="../Controllers/MovieController.php?action=addVote">
                            <input type="hidden" name="vote" value="like">
                            <input type="hidden" name="movieId" value="{$movie['id']}">
                            <button type="submit" $buttonLikeDisable style="$hiddenStyle $hiddenLikeStyle">Like</button>
                        </form>
                        <p id="seperator" style="$hiddenStyle">|</p>

                        <!-- Hate button form -->
                        <form method="POST" action="../Controllers/MovieController.php?action=addVote">
                            <input type="hidden" name="vote" value="hate">
                            <input type="hidden" name="movieId" value="{$movie['id']}">
                            <button type="submit" $buttonHateDisable style="$hiddenStyle $hiddenHateStyle">Hate</button>
                        </form>
                    </div>
                    <!-- Filter button form -->
                    <div class="username">
                        <p>Posted By: </p>
                        <form method="POST" action="../Controllers/MovieController.php?action=getFilteredMoviesData">
                            <input type="hidden" name="user_name" value="{$movie['user_name']}">
                            <button type="submit">{$displayUserName}</button>
                        </form>
                    </div>
                </div>
            </div>
        HTML;

        // Reset variables for the next movie
        $hiddenLikeStyle = "";
        $buttonLikeDisable = "";

        $hiddenHateStyle = "";
        $buttonHateDisable = "";

        $hiddenStyle = (isset($_SESSION['user'])) ? "" : "display: none;";

        $isLike = null;
    }
}

//endregion

//region DisplayHeader

/**
 * Displays the header section of the page.
 *
 * If a user is logged in (i.e., 'user' is set in the session), it displays a welcome message with the user's name.
 * Otherwise, it displays links to the login and signup pages.
 *
 * @return void
 */
function displayHeader()
{

    // Check if the user is logged in by verifying if 'user' exists in the session
    if (isset($_SESSION['user'])) {

        $userName = $_SESSION['user'] ?? [];

        // Display a welcome message with the username and a logout button
        echo <<<HTML
        <div class="UserHeader">
            <p><span id="welcomeSpan">Welcome: </span><span id="usernameSpan">$userName</span></p>

            <!-- Start of Logout Button -->
            <div class="Logout">
                <form method="POST" action="../Controllers/UserController.php?action=logout">
                    <button type="submit">Logout</button>
                </form>
            </div>
            <!-- End of Logout Button-->
        </div>
        HTML;
    } else {

        // If the user is not logged in, display links to the Login and Signup pages
        echo <<<HTML
            <div class="IndexHeader">
                <a href="Login.php"><span id="loginSpan">Log in</span></a>
                <p>or</p>
                <a href="Signup.php"><span id="singupSpan">Sign Up</span></a>
            </div>    
        HTML;
    }
}
//endregion

//region DisplayTotalMovies

/**
 * Displays the total number of movies found.
 *
 * If the 'movies' session variable is not set, it calls the MovieController to fetch the movies data.
 * Then, it counts the number of movies and displays the total count.
 *
 * Outputs the total number of movies as a paragraph element.
 */
function displayTotalMovies()
{
    if (!isset($_SESSION['movies'])) {
        $controller = new MovieController();
        $controller->getMoviesData();
    }

    $movies = $_SESSION['movies'] ?? [];
    $totalMovies = count($movies);

    echo <<<HTML
        <p>Found $totalMovies movies</p>
    HTML;
}
//endregion

//region DisplayMainBodyRightSection

/**
 * Displays the right section of the main body on the index page.
 *
 * This section includes:
 * - An "Add Movie" button (visible only to logged-in users).
 * - A set of filter options for sorting movies by likes, hates, or publication date.
 *
 * The filters are available to all users, while the "Add Movie" button is shown
 * only if a user is logged in (i.e., $_SESSION['user'] is set).
 *
 * Outputs the corresponding HTML directly.
 */
function displayMainBodyRightSection()
{

    if (isset($_SESSION['user'])) {

        echo <<<HTML
            <!-- Start of Add Movie Section -->
            <div class="AddMovie">
                    <a href="AddMovie.php"><button type="submit">New Movie</button></a>
            </div>
            <!-- End of Add Movie Section -->

            <!-- Start of Filters Section -->
            <div class="filters">

                <!-- Start of Filter's Title Section-->
                <div class="sortBy">
                    <div class="row">
                        <p>Sort By:</p>
                    </div>
                    <hr>
                </div>
                <!-- End of Filter's Title Section-->

                <!-- Start of Filter's Like Section-->
                <div class="likes">
                    <div class="row">
                        <p id="filterRow">Likes</p>
                        <form method="POST" action="../Controllers//MovieController.php?action=sortMovies">
                            <input type="hidden" name="sort" value="likes">
                            <button type="submit"></button>
                        </form>
                    </div>
                    <hr>
                </div>
                <!-- End of Filter's Likes Section-->

                <!-- Start of Filter's Hates Section-->
                <div class="hates">
                    <div class="row">
                        <p id="filterRow">Hates</p>
                        <form method="POST" action="../Controllers//MovieController.php?action=sortMovies">
                            <input type="hidden" name="sort" value="hates">
                            <button type="submit"></button>
                        </form>
                    </div>
                    <hr>
                </div>
                <!-- End of Filter's Hates Section-->

                <!-- Start of Filter's Dates Section-->
                <div class="dates">
                    <div class="row">
                        <p id="filterRow">Dates</p>
                        <form method="POST" action="../Controllers//MovieController.php?action=sortMovies">
                            <input type="hidden" name="sort" value="publication_date">
                            <button type="submit"></button>
                        </form>
                    </div>
                </div>
                <!-- End of Filter's Dates Section-->
            </div>
            <!-- End of Filters Section-->

            <!-- Start of All Movies Section -->
            <div class="AllMovies">
                <form method="POST" action="../Controllers/MovieController.php?action=getMoviesData">
                    <button type="submit">All Movies</button>
                </form>
            </div>
            <!-- End of All Movies Section -->

        HTML;
    } else {

        echo <<<HTML
            <!-- Start of Filters Section-->
            <div class="filters">

                <!-- Start of Filter's Title Section-->
                <div class="sortBy">
                    <div class="row">
                        <p>Sort By:</p>
                    </div>
                    <hr>
                </div>
                <!-- End of Filter's Title Section-->

                <!-- Start of Filter's Like Section-->
                <div class="likes">
                    <div class="row">
                        <p id="filterRow">Likes</p>
                        <form method="POST" action="../Controllers/MovieController.php?action=sortMovies">
                            <input type="hidden" name="sort" value="likes">
                            <button type="submit"></button>
                        </form>
                    </div>
                    <hr>
                </div>
                <!-- End of Filter's Likes Section-->

                <!-- Start of Filter's Hates Section-->
                <div class="hates">
                    <div class="row">
                        <p id="filterRow">Hates</p>
                        <form method="POST" action="../Controllers/MovieController.php?action=sortMovies">
                            <input type="hidden" name="sort" value="hates">
                            <button type="submit"></button>
                        </form>
                    </div>
                    <hr>
                </div>
                <!-- End of Filter's Hates Section-->

                <!-- Start of Filter's Dates Section-->
                <div class="dates">
                    <div class="row">
                        <p id="filterRow">Dates</p>
                        <form method="POST" action="../Controllers/MovieController.php?action=sortMovies">
                            <input type="hidden" name="sort" value="publication_date">
                            <button type="submit"></button>
                        </form>
                    </div>
                </div>
                <!-- End of Filter's Dates Section-->
            </div>
            <!-- End of Filters Section-->

             <!-- Start of All Movies Section -->
            <div class="AllMovies">
                <form method="POST" action="../Controllers/MovieController.php?action=getMoviesData">
                    <button type="submit">All Movies</button>
                </form>
            <!-- End of All Movies Section -->
        HTML;
    }
}
//endregion

?>


<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Styles/Index.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <title>MovieWorld</title>
</head>

<body>

    <!-- Start of Header  -->
    <div class="header">

        <h1>Movie World</h1>

        <?= displayHeader() ?>

    </div>
    <!-- End of Header -->

    <!-- Start of Total Movies Section-->
    <div class="totalMovies">
        <?= displayTotalMovies() ?>
    </div>
    <!-- End of Total Movies Section-->

    <!-- Start of Main Body Section-->
    <div class="mainBody">

        <!-- Start of Main Body's Movies Section-->
        <div class="movies">
            <?= displayMovies() ?>
        </div>
        <!-- End of Main Body's Movies Section-->

        <!-- Start of Main Body's Right Section -->
        <div class="rightSection">
            <?= displayMainBodyRightSection() ?>
        </div>
        <!-- End of Main Body's Right Section -->

    </div>
    <!-- End of Main Body Section-->
</body>

</html>