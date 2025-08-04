<?php

namespace Views;

require_once("../Services/MovieService.php");

use Services\MovieService;

session_start();

//region SetMovies

/**
 * Checks if the "movies" data is set in the session.
 * If not, it creates an instance of MovieService and fetches movies.
 *
 * This function ensures that the movies are available in the session
 * for later use, reducing redundant database or API calls.
 */
function setMovies()
{

    // If movies are not set in the session, fetch them using the MovieService
    if (!isset($_SESSION["movies"])) {
        $service = new MovieService();
        $service->getMovies();
    }
}
//endregion

//region DisplayMovies

/**
 * Displays a list of movies with their details, including title, publication date,
 * description, likes, hates, and the username of the poster. Also provides forms
 * to submit "Like" or "Hate" votes for each movie.
 *
 * Retrieves movies from the session if available; otherwise, fetches them using
 * the MovieService. Outputs the movie information as HTML.
 *
 */
function displayMovies()
{
    setMovies();

    $cnt = 0;

    $service = new MovieService();
    $votes = $service->getUserVotings();
    $votesArray = array_values($votes);

    // Retrieve movies from the session, or use an empty array if not set
    $movies = $_SESSION["movies"] ?? [];

    // Get the current logged-in user's name from the session, or set to empty string if not set
    $userName = $_SESSION["user"] ?? [];

    $hiddenStyle = (empty($userName)) ? "display: none;" : "";

    $hiddenLikeStyle = "";
    $buttonLikeDisable = "";

    $hiddenHateStyle = "";
    $buttonHateDisable = "";


    // Loop through each movie and display its details
    foreach ($movies as $movie) {



        if ($userName === $movie->user_name) {
            $movie->user_name = "You" ?? '';
            $hiddenStyle = "display: none;";
        }

        if ($cnt < count($votesArray)) {

            $currentVote = $votesArray[$cnt];

            if ($currentVote['movie_id'] == $movie->id && $currentVote['is_like'] == 1) {

                $buttonLikeDisable = 'disabled';
                $hiddenLikeStyle = 'background-color: lightgray;';
            }
            if ($currentVote['movie_id'] == $movie->id && $currentVote['is_hate'] == 1) {

                $buttonHateDisable = 'disabled';
                $hiddenHateStyle = 'background-color: lightgray;';
            }
        }

        // Output the movie information using heredoc syntax
        echo <<<HTML
            <div class="movieInfo">
                <div class="topSection">
                    <h1>$movie->title</h1>
                    <p>Posted: $movie->publication_date</p>
                </div>

                <div class="middleSection">
                    <p>$movie->description</p>
                </div>

                <div class="bottomSection">

                    <div class="votes">
                        <p>$movie->likes</p>
                        <p id="seperator">|</p>
                        <p>$movie->hates</p>
                    </div>

                    <div class="buttons">
                        <!-- Like button form -->
                        <form method="POST" action="../Controllers/MovieController.php?action=addVote">
                            <input type="hidden" name="vote" value="like">
                            <input type="hidden" name="movieId" value="{$movie->id}">
                            <button type="submit" $buttonLikeDisable style="$hiddenStyle $hiddenLikeStyle">Like</button>
                        </form>
                        <p id="seperator" style="$hiddenStyle">|</p>

                        <!-- Hate button form -->
                        <form method="POST" action="../Controllers/MovieController.php?action=addVote">
                            <input type="hidden" name="vote" value="hate">
                            <input type="hidden" name="movieId" value="{$movie->id}">
                            <button type="submit" $buttonHateDisable style="$hiddenStyle $hiddenHateStyle">Hate</button>
                        </form>
                    </div>

                    <div class="username">
                        <p>Posted By: </p>
                        <form method="POST" action="../Controllers/MovieController.php?action=getMovies">
                            <input type="hidden" name="user_name" value="{$movie->user_name}">
                            <button type="submit">$movie->user_name</button>
                        </form>
                    </div>
                </div>
            </div>
        HTML;

        $hiddenLikeStyle = "";
        $buttonLikeDisable = "";

        $hiddenHateStyle = "";
        $buttonHateDisable = "";

        $cnt++;
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
            <p><span id="welcomeSpan">Welcome Back</span><span id="usernameSpan">$userName</span></p>

            <!-- Start of Logout Button -->
            <div class="Logout">
                    <a href="Login.php"><button>Logout</button></a>
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
 * This function initializes the movies list by calling setMovies(),
 * retrieves the movies array from the session, counts the total number
 * of movies, and outputs the result in a paragraph element.
 */
function displayTotalMovies()
{

    setMovies();

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
                <a href="Index.php"><button>All Movies</button></a>
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
                <a href="Index.php"><button>All Movies</button></a>
            </div>
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

<?php unset($_SESSION['movies']); ?>