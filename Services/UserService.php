<?php

namespace Model;

require_once __DIR__ . '/../Database/DbConnection.php';

use Database\DbConnection;


class UserService
{

    private $connection;

    private $exists = false;
    private $passwordVerified = false;
    private $passwordValid = false;

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

        // Store the user's username in the session
        $_SESSION['user'] = $_POST['username'] ?? null;

        // Get all POST data
        $data = $_POST;

        // Check if the username already exists in the database
        if($this->isUsernameExists($data['username'])){
            $this->exists = true;
            $_SESSION['error_message'] = "Username Exists";
            return;
        }
        
        if($this->isPasswordValid($data['password'])){

        $this->passwordValid = true;
            
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

        // Verify if the username exists in the database
        if (!$this->isUsernameExists($username)){
            $_SESSION["error_message"] = "Wrong Username";
            return;
        }
        // Check if the provided password matches the stored hash
        if (!$this->isPasswordVerified($username, $password)) {
            $_SESSION["error_message"] = "Wrong Password";
            return;
        }

        // Store the user's username in the session
        $_SESSION['user'] = $username;

        // Set flags indicating the user exists and the password is verified
        $this->exists = true;
        $this->passwordVerified = true;
    }

    //endregion

    //region Logout

    /**
     * Logs out the user by unsetting the user session variable.
     *
     * This method is called when the user clicks the logout button,
     * effectively ending the user's session.
     *
     * @return void
     */

    public function logout(){

        unset($_SESSION['user']); // Remove the user from the session
        session_destroy(); // Destroy the entire session
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

    //region PasswordValidation

    /**
     * Validates the provided password against a set of criteria.
     *
     * The password must contain at least one uppercase letter, one lowercase letter,
     * one number, one special character, and be at least 8 characters long.
     *
     * @param string $password The password to validate.
     * @return bool Returns true if the password is valid, false otherwise.
     */
    private function isPasswordValid($password){

        // Define a regex pattern for password validation
        $passwordPattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/";

        // Check if the password matches the pattern
        if (!preg_match($passwordPattern, $password)) {
            if (!preg_match('/[A-Z]/', $password))
                $_SESSION['error_message'] = "Password must contain at least one uppercase letter. <br>";
            if (!preg_match('/[a-z]/', $password))
                $_SESSION['error_message'] .= "Password must contain at least one lowercase letter. <br>";
            if (!preg_match('/\d/', $password))
                $_SESSION['error_message'] .= "Password must contain at least one number. <br>";
            if (!preg_match('/[@$!%*?&]/', $password))
                $_SESSION['error_message'] .= "Password must contain at least one special character. <br>";
            if (strlen($password) < 8)
                $_SESSION['error_message'] .= "Password must be at least 8 characters long. <br>";

            return false; // Password is Invalid

        } else {
            return true; // Password is Valid
        }
    }
    //endregion 

    //region Getters

    /**
     * Get the value of exists
     */ 
    public function getExists()
    {
        return $this->exists;
    }

    /**
     * Get the value of passwordVerified
     */ 
    public function getPasswordVerified()
    {
        return $this->passwordVerified;
    }

    /**
     * Get the value of passwordValid
     */ 
    public function getPasswordValid()
    {
        return $this->passwordValid;
    }

    //endregion

}
