<?php

namespace Controllers;

require_once("../Services/UserService.php");

use Model\UserService;

class UserController
{

    private $service;

    public function __construct()
    {
        $this->service = new UserService();
    }

    //region CreateUser

    /**
     * Handles the creation of a new user by delegating to the service layer.
     * After creating the user, redirects to the TestUserCreateView page.
     *
     * @return void
     */
    public function createUser()
    {
        $this->service->createUser();

        if ($this->service->getExists()) {
            
            header("Location: ../Views/Signup.php");
            exit;
        }
            

        header("Location: ../Views/Index.php");
        exit;
    }

    //endregion

    //region UserLogin

    /**
     * Handles the user login process.
     *
     * Calls the userLogin method from the service layer to authenticate the user,
     * then redirects to the TestUserLogin view.
     *
     * @return void
     */
    public function userLogin()
    {
        // Attempt to authenticate the user using the service layer
        $this->service->userLogin();

        // If the username does not exist, set an error message and redirect to the login page
        if (!$this->service->getExists()) {

            header("Location: ../Views/Login.php");
            exit;
        }

        // If the password is incorrect, set an error message and redirect to the login page
        if (!$this->service->getPasswordVerified()){

            header("Location: ../Views/Login.php");
            exit;
        }

        // If authentication is successful, redirect to the main view
        header("Location: ../Views/Index.php");
        exit;
    }

    //endregion

    //region UserLogout
    /**
     * Handles the user logout process.
     *
     * Calls the logout method from the service layer to log out the user,
     * then redirects to the login page.
     *
     * @return void
     */
    public function logout()
    {
        // Call the logout method from the service layer
        $this->service->logout();

        // Redirect to the login page after logging out
        header("Location: ../Views/Index.php");
        exit;
    }   
    //endregion
}

//region Controller's Starting Point

/**
 * Handles POST requests for user-related actions.
 * 
 * This block checks if the request method is POST and an 'action' parameter is set in the URL.
 * It then creates an instance of UserController, starts a session, and dynamically calls
 * the method specified by the 'action' parameter.
 */

if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_GET['action'])) {

    // Create a new instance of the UserController
    $controller = new UserController();

    // Get the action name from the URL query parameter
    $action = $_GET['action'];

    // Start a new or resume the existing session
    session_start();

    // Dynamically call the method on the controller based on the action parameter
    $controller->$action();
}

//endregion
