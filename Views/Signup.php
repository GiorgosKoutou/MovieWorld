<?php 
session_start();

if(isset($_SESSION["error_message"])) {

    // Display the error message if it exists
    echo $_SESSION["error_message"] ?? '';
    unset($_SESSION["error_message"]);
}


?>


<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="../Styles/Signup.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
</head>
<body>
    
    <div class="SignUpForm">

        <h1>Sign Up</h1>

        <form method="POST" action="../Controllers/UserController.php?action=createUser">

            <div class="name">
                <label>Name:</label>
                <input type="text" name="name">
            </div>
            <div class="username">
                <label>Username:</label>
                <input type="text" name="username" required>
            </div>

            <div class="password">
                <label>Password:</label>
                <input type="password" name="password" autocomplete="off" required>
            </div>

            <button type="submit">Sign Up</button></a>
        </form>
    </div>

</body>
</html>