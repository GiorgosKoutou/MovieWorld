<?php

namespace Model;

require_once("../Database/DbConnection.php");
require_once("../Database/Models/User.php");

use Database\DbConnection;
use Database\Models\User;

class UserService
{

    private $connection;

    public $exists = true;
    public $passwordVerified = true;

    public function __construct()
    {

        $this->connection = DbConnection::connect();
    }

    //region CreateUser

    /**
     * Creates a new user in the database.
     *
     * Retrieves user data from the POST request, hashes the password,
     * and inserts the new user record into the 'users' table.
     *
     * @throws PDOException If the database operation fails.
     */
    public function createUser()
    {
        // Store the username in the session
        $_SESSION['user'] = $_POST['username'] ?? [];

        // Get all POST data
        $data = $_POST;

        // Check if the username already exists in the database
        if($this->isUsernameExists($data['username'])){
            $this->exists = true;
            return;
        }
            
        // Hash the user's password before storing
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        // Prepare the SQL query to insert a new user
        $query = "INSERT INTO users(name, username, password)
                    VALUES(:name, :username, :password)";

        // Prepare the statement
        $stm = $this->connection->prepare($query);

        // Execute the statement with the user data
        $stm->execute($data);
    }

    //endregion

    //region UsernameExists

    /**
     * Checks if a username already exists in the users table.
     *
     * @param string $username The username to check for existence.
     * @return bool Returns true if the username exists, false otherwise.
     */
    private function isUsernameExists($username){

        // Prepare SQL query to check if the username exists in the users table
        $query = "SELECT username FROM users WHERE username = :username";
        $stm = $this->connection->prepare($query);

        // Execute the statement with the provided username
        $stm->execute(["username"=> $username]);

        // Fetch the result (will be false if no row is found)
        $isExists = $stm->fetch();

        // If a row is found, the username exists
        if($isExists)
            return true;

        // Username does not exist
        return false;
    }

    //endregion

    //region PasswordVerified

    /**
     * Verifies if the provided password matches the hashed password stored for the given username.
     *
     * @param string $username The username whose password is to be verified.
     * @param string $password The plain text password to verify.
     * @return bool Returns true if the password is correct, false otherwise.
     */
    private function isPasswordVerified($username, $password){

        // Prepare SQL query to fetch the hashed password for the given username
        $query = "SELECT password FROM users WHERE username = :username";
        $stm = $this->connection->prepare($query);

        // Execute the statement with the provided username
        $stm->execute(["username" => $username]);

        // Fetch the hashed password from the result
        $hasedPassword = $stm->fetch();

        // Verify the provided password against the hashed password
        if (password_verify($password, $hasedPassword["password"]))
            return true;

        // Return false if the password does not match
        return false;
    }

    //endregion

    //region UserLogin

    /**
     * Handles user login by verifying the provided username and password.
     *
     * Retrieves the username and password from the POST request, checks if the user exists in the database,
     * and verifies the password using password hashing. If authentication is successful, stores the username
     * in the session. Outputs error messages for invalid username or password.
     *
     * @return void
     */
    public function userLogin()
    {

        // Retrieve username and password from POST request
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Store the username in the session
        $_SESSION['user'] = $username; 

        // Verify if the username exists in the database
        if (!$this->isUsernameExists($username)){
            $this->exists = false;
            return;
        }
        // Check if the provided password matches the stored hash
        if (!$this->isPasswordVerified($username, $password)) {
            $this->passwordVerified = false;
            return;
        }
    }

    //endregion
}
