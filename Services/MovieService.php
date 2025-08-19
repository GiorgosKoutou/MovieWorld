<?php

namespace Services;

require_once __DIR__ . '/../Database/DbConnection.php';

use Database\DbConnection;

class MovieService
{

   private $connection;


   public function __construct()
   {

      $this->connection = DbConnection::connect();
   }

   /*********** CRUD OPERATIONS ***********/

   //region GetMovies

   /**
    * Retrieves movies from the database based on the provided user name.
    * If a 'user_name' is present in the POST request, fetches movies for that user only.
    * Otherwise, fetches all movies.
    * The resulting movies are stored in the session under the 'movies' key.
    *
    * @return void
    */

   public function getMoviesData()
   {

      // Unset the 'movies' session variable to clear any previous movie data
      unset($_SESSION['movies']);

      // If a user name is provided, fetch movies for that user only
      if (isset($_SESSION['usernameFilter'])) {

         $username = $_SESSION['usernameFilter'];

         // Prepare SQL query to select movies by user name
         $query = "SELECT * FROM movies WHERE user_name = :userName";
         $stm = $this->connection->prepare($query);

         // Execute the query with the provided user name
         $stm->execute(['userName' => $username]);

         // Fetch all movies as Movie objects for the user
         $moviesByUser = $stm->fetchAll($this->connection::FETCH_ASSOC);

         // Store the movies in the session
         $_SESSION['movies'] = $moviesByUser;

         return;
      }

      // If no user name is provided, fetch all movies
      $query = "SELECT * FROM movies";
      $stm = $this->connection->prepare($query);
      $stm->execute();

      // Fetch all movies as Movie Associative array
      $allMovies = $stm->fetchAll($this->connection::FETCH_ASSOC);

      if (empty($allMovies)) {

         // If no movies are found, store the error message in the session
         $_SESSION['empty_data'] = "No movies found";
         return;
      }

      // Store the movies in the session
      $_SESSION['movies'] = $allMovies;

   }

   //endregion

   //region AddMovies

   /**
    * Adds a new movie to the database using data from the $_POST array.
    * After insertion, redirects to the TestView.php page.
    *
    * @return void
    */

   public function addMovie()
   {
      // Check if the movie with the same title already exists in the database
      $result = $this->findMovieByTitle($_POST['title']);

      // If a movie with the same title already exists, set an error message in the session
      if($result){

         $_SESSION['movie_exists'] = "Movie with this title already exists";
         return;
      }


      // Prepare the SQL query to insert a new movie with initial likes and hates set to 0
      $insertQuery = "INSERT INTO movies (title, description, user_name, publication_date, likes, hates)
                     VALUES (:title, :description, :userName, :publicationDate, 0, 0)";

      // Prepare the statement for execution
      $stm = $this->connection->prepare($insertQuery);

      // Execute the statement with data from the $_POST array
      $stm->execute($_POST);

      // If the insertion is successful, fetch the last inserted movie ID
      $movieId = $this->connection->lastInsertId();
      $insertedMovie = $this->findMovieById($movieId);

      $_SESSION['movies'] = [$insertedMovie];
   }

   //endregion

   //region AddVote

   /**
    * Handles voting (like or hate) for a movie.
    *
    * This method retrieves the movie ID from the POST request and the vote type ('like' or 'hate') from the GET request.
    * Depending on the vote type, it increments the corresponding 'likes' or 'hates' count for the specified movie in the database.
    * After updating the vote, it redirects the user to the TestView page.
    *
    * @return void
    */

   public function addVote()
   {
      $this->connection->beginTransaction();

      try {
         // Retrieve the movie ID and the Username from POST data
         $movieId = $_POST['movieId'] ?? null;
         $userName = $_SESSION['user'] ?? [];

         $isLike = false;
         $isHate = false;

         // Retrieve the vote type ('like' or 'hate') from GET data
         $vote = $_POST['vote'];

         // If the vote is a 'like'
         if (isset($vote) && $vote === 'like') {

            $isLike = true;

            // Check if the user has previously hated this movie
            $checkQuery = "SELECT movie_id, is_hate FROM votings WHERE user_name = :username AND movie_id = :movieId";
            $stm = $this->connection->prepare($checkQuery);
            $stm->execute(["username" => $userName, 'movieId' => $movieId]);
            $result = $stm->fetch();

            if ($result && $result['is_hate']) {

               // If the user previously hated, decrement 'hates' and increment 'likes'
               $updateMoviesQuery = "UPDATE movies SET likes = likes + 1, hates = hates - 1 WHERE id = :movieId";
               $stm = $this->connection->prepare($updateMoviesQuery);

               // Execute the query with the movie ID
               $stm->execute(['movieId' => $movieId]);

               $updateVotingQuery = "UPDATE votings SET is_like = :isLike, is_hate = :hate WHERE movie_id = :movieId AND user_name = :username";

               $stm = $this->connection->prepare($updateVotingQuery);
               $stm->execute(['isLike' => $isLike, 'hate' => $isHate, 'movieId' => $movieId, 'username' => $userName]);
            } else {

               // If not, just increment 'likes'
               $updateQuery = "UPDATE movies SET likes = likes + 1 WHERE id = :movieId";
               $stm = $this->connection->prepare($updateQuery);

               // Execute the query with the movie ID
               $stm->execute(['movieId' => $movieId]);

               $insertVotingQuery = "INSERT INTO votings (movie_id, user_name, is_like, is_hate) 
                                       VALUES(:movieId, :username, :isLike, :hate)";

               $stm = $this->connection->prepare($insertVotingQuery);
               $stm->execute(['movieId' => $movieId, 'username' => $userName, 'isLike' => $isLike, 'hate' => $isHate]);
            }
         }

         // If the vote is a 'hate'
         if (isset($vote) && $vote === 'hate') {

            $isHate = true;

            // Check if the user has previously liked this movie
            $checkQuery = "SELECT movie_id, is_like  FROM votings WHERE user_name = :username AND movie_id = :movieId";
            $stm = $this->connection->prepare($checkQuery);
            $stm->execute(["username" => $userName, 'movieId' => $movieId]);
            $result = $stm->fetch();

            if ($result && $result['is_like']) {

               // If the user previously liked, decrement 'likes' and increment 'hates'
               $query = 'UPDATE movies SET hates = hates + 1, likes = likes - 1 WHERE id = :movieId';
               $stm = $this->connection->prepare($query);
               $stm->execute(['movieId' => $movieId]);

               $updateVotingQuery = "UPDATE votings SET is_like = :isLike, is_hate = :hate WHERE movie_id = :movieId AND user_name = :username";

               $stm = $this->connection->prepare($updateVotingQuery);
               $stm->execute(['isLike' => $isLike, 'hate' => $isHate, 'movieId' => $movieId, 'username' => $userName]);
            } else {

               // If not, just increment 'hates'
               $query = 'UPDATE movies SET hates = hates + 1 WHERE id = :movieId';
               $stm = $this->connection->prepare($query);
               $stm->execute(['movieId' => $movieId]);

               $insertQuery = "INSERT INTO votings (movie_id, user_name, is_like, is_hate) 
                                       VALUES(:movieId, :username, :isLike, :hate)";

               $stm = $this->connection->prepare($insertQuery);
               $stm->execute(['movieId' => $movieId, 'username' => $userName, 'isLike' => $isLike, 'hate' => $isHate]);
            }
         }

         $this->connection->commit();
      } catch (\Exception $e) {

         $this->connection->rollback();
      }
   }


   //endregion

   //region SortMovies

   /**
    * Sorts movies based on the specified sort parameter.
    * If a user name filter is set in the session, it sorts movies for that user only.
    * Otherwise, it sorts all movies.
    *
    * @return void
    */
   public function sortMovies()
   {

      // Get the sort parameter from POST data if available, otherwise set to null
      $sortParam = $_POST['sort'] ?? null;

      // If a sort parameter is provided and the session has a username filter
      if (!empty($_SESSION['usernameFilter'])) {

         // Prepare SQL query to select all movies ordered by the specified sort parameter in descending order
         $query = "SELECT * FROM movies WHERE user_name = :username ORDER BY $sortParam DESC";

         // Prepare the statement for execution
         $stm = $this->connection->prepare($query);

         // Execute the statement
         $stm->execute(['username' => $_SESSION['usernameFilter']]);

         // Fetch all movies by a specific user as Movie Associative array
         $sortedMoviesByUser = $stm->fetchAll($this->connection::FETCH_ASSOC);

         // Store the sorted movies in the session
         $_SESSION['movies'] = $sortedMoviesByUser;

         return;
      }

      // Prepare SQL query to select all movies ordered by the specified sort parameter in descending order
      $query = "SELECT * FROM movies ORDER BY $sortParam DESC";

      // Prepare the statement for execution
      $stm = $this->connection->prepare($query);

      // Execute the statement
      $stm->execute();

      // Fetch all movies as Associative array
      $sortedMovies = $stm->fetchAll($this->connection::FETCH_ASSOC);

      // Store the sorted movies in the session
      $_SESSION['movies'] = $sortedMovies;
   }

   //endregion

   //region CheckVotes

   /**
    * Checks if the current user has voted on a specific movie.
    *
    * This function retrieves the user's votes from the session and checks if they have
    * liked or hated the specified movie. Returns true if the user has liked the movie,
    * false if they have hated it, or null if they haven't voted.
    *
    * @param int $movieId The ID of the movie to check votes for.
    * @return bool Returns true if liked, false if hated, or null if no vote found.
    */
   public function checkVotes($movieId)
   {

      $votes = $this->getUserVotings();

      foreach ($votes as $vote) {
         if ($vote['movie_id'] === $movieId && $vote['is_like'] == true)
            return true;

         if ($vote['movie_id'] === $movieId && $vote['is_hate'] == true)
            return false;
      }
   }
   //endregion

   //region GetUserVotings
   /**
    * Retrieves the voting records of the current user from the database.
    *
    * This function fetches all voting records for the user stored in the session.
    * It returns an array of voting records, each containing movie ID, like status, and hate status.
    *
    * @return array Returns an array of voting records for the user.
    */
   private function getUserVotings()
   {

      $userName = $_SESSION['user'] ?? null;

      $query = 'SELECT movie_id, is_like, is_hate FROM votings WHERE user_name = :username';
      $stm = $this->connection->prepare($query);
      $stm->execute(['username' => $userName]);

      return $stm->fetchAll();
   }
   //endregion

   //region FindMovieByTitle

   /**
    * Finds a movie by its title in the database.
    *
    * This function prepares and executes a SQL query to find a movie with the specified title.
    * It returns the movie data if found, or null if not found.
    *
    * @param string $title The title of the movie to search for.
    * @return array|null Returns an associative array of movie data or null if not found.
    */
   private function findMovieByTitle($title)
   {
      $query = "SELECT * FROM movies WHERE title = :title";
      $stm = $this->connection->prepare($query);
      $stm->execute(['title' => $title]);

      return $stm->fetch();
   }
   //endregion

   //region FindMovieById

   /**
    * Finds a movie by its ID in the database.
    *
    * This function prepares and executes a SQL query to find a movie with the specified ID.
    * It returns the movie data if found, or null if not found.
    *
    * @param int $id The ID of the movie to search for.
    * @return array|null Returns an associative array of movie data or null if not found.
    */
   private function findMovieById($id)
   {
      $query = "SELECT * FROM movies WHERE id = :id";
      $stm = $this->connection->prepare($query);
      $stm->execute(['id' => $id]);

      return $stm->fetch();
   }
   //endregion

}
