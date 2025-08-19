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
        $this->service->addMovie();
        $this->service->getMoviesData();
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
