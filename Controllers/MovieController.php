<?php

namespace Controllers;

require_once __DIR__ . "/../Services/MovieService.php";

use Services\MovieService;

class MovieController
{

    private $service;

    public function __construct()
    {
        $this->service = new MovieService();
    }

    //region GetMovies

    /**
     * Retrieves a list of movies using the service layer and redirects to the TestView page.
     *
     * This method calls the getMoviesData() function from the service to fetch movie data,
     * then redirects the user to the TestView.php page. The script execution is terminated
     * after the redirect.
     *
     * @return void
     */
    public function getMoviesData()
    {
        unset($_SESSION['usernameFilter']);

        $this->service->getMoviesData();
        header("Location: ../Views/Index.php");
        exit;
    }

    //endregion

    //region GetFilteredMoviesData

    /**
     * Retrieves filtered movie data based on the username provided in the POST request.
     * This method updates the session with the username filter and redirects to the Index page.
     *
     * @return void
     */
    public function getFilteredMoviesData()
    {
        $_SESSION['usernameFilter'] = $_POST['user_name'];

        $this->service->getMoviesData();
        header("Location: ../Views/Index.php");
        exit;
    }
    //endregion

    //region AddMovie

    /**
     * Handles the addition of a new movie by delegating to the service layer.
     * After adding the movie, redirects the user to the TestView page.
     *
     * @return void
     */
    public function addMovie()
    {
        if(isset($_SESSION['usernameFilter'])) 
            unset($_SESSION['usernameFilter']);

        $this->service->addMovie();
        header("Location: ../Views/Index.php");
        exit;
    }

    //endregion

    //region AddVote

    /**
     * Handles the process of adding a vote to a movie.
     *
     * This method delegates the vote addition to the service layer and then redirects
     * the user to the TestView page. It is typically triggered by a user action such as
     * submitting a vote form.
     *
     * @return void
     */
    public function addVote()
    {
        $this->service->addVote();
        $this->service->getMoviesData();
        header("Location: ../Views/index.php");
        exit;
    }

    //endregion

    //region SortMovies

    /**
     * Sorts the list of movies using the service layer and redirects to the TestView page.
     *
     * This method calls the sortMovies() function from the service to sort movies,
     * then redirects the user to the TestView.php page. Execution is terminated after redirection.
     *
     * @return void
     */
    public function sortMovies()
    {
        $this->service->sortMovies();

        if (isset($_SESSION['user'])) {

            header("Location: ../../Views/index.php");
            exit;
        }

        header("Location: ../Views/index.php");
        exit;
    }

    //endregion

    //region CheckVotes

    /**
     * Checks the votes for a specific movie and redirects to the index page.
     *
     * This method calls the checkVotes() function from the service to verify votes
     * for a given movie ID, then redirects the user to the index.php page.
     *
     * @param int $movieId The ID of the movie to check votes for.
     * @return void
     */
    public function checkVotes($movieId)
    {
        return $this->service->checkVotes($movieId);
    }
    //endregion

}

//region Controller's Starting Point

/**
 * Handles POST requests with a specified 'action' parameter in the query string.
 * Instantiates the MovieController, starts a session, and dynamically calls the requested action method.
 */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_GET['action'])) {

    // Instantiate the MovieController
    $controller = new MovieController();

    // Get the action from the query string
    $action = $_GET['action'];

    // Start the session to manage user data
    session_start();

    // Dynamically call the requested action method on the controller
    $controller->$action();
}

//endregion 
