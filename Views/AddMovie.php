<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Styles/AddMovie.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <title>Add Movie</title>
</head>

<body>
    <div class="AddMovieForm">
        <h1>Add Movie</h1>
        <form method="POST" action="../Controllers/MovieController.php?action=addMovie">

            <div class="title">
                <label>Title:</label>
                <input type="text" name="title" required>
            </div>

            <div class="description">
                <label>Description:</label>
                <textarea name="description" rows="5" cols="50"></textarea>
            </div>

            <div class="name">
                <label>Username:</label>
                <input type="text" name="userName" required>
            </div>

            <div class="date">
                <label>Publication Date:</label>
                <input type="date" name="publicationDate">
            </div>

            <button type="submit">Add Movie</button>
        </form>
    </div>
</body>

</html>