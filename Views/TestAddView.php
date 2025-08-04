<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Testing</title>
</head>
<body>
    <form method="POST" action="../Controllers//MovieController.php?action=addMovie">
        <label>Title</label>
        <input type="text" name="title" required>
        <label>Description</label>
        <input type="text" name="description">
        <label>UserName</label>
        <input type="text" name="userName" required>
        <label>Publication Date</label>
        <input type="date" name="publicationDate">
        <button type="submit">Add Movie</button>
    </form>
</body>
</html>