<?php 

session_start();
if (isset($_SESSION['error_message'])){
    
    echo "<div style='color: red;'>" . $_SESSION['error_message'] . "</div>";
    unset($_SESSION['error_message']);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
    <form method="POST" action="../Controllers//UserController.php?action=createUser">
        <label>Name</label>
        <input type="text" name="name" required>
        <label>Username</label>
        <input type="text" name="username" required>
        <label>Password</label>
        <input type="password" autocomplete="new-password" name="password" required>
        <button type="submit">Add User</button>
    </form>

</body>
</html>