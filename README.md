# MovieWorld

**MovieWorld** is a simple social sharing platform that allows users to submit their favorite movies, view movies added by others, and express their opinion by liking or hating each entry. Visitors can freely browse and sort the movie list, while registered users can participate more actively by adding content and voting.

---

## Features

- **User Authentication**
  - Users can sign up and log in to access full functionality.
  
- **Movie Submission**
  - Logged-in users can add new movies by providing a title and description.
  - Each movie is associated with the submitting user and the submission date.

- **Voting System**
  - Users can express their opinion by liking or hating a movie.
  - A user can only vote once per movie.
  - Users can change their vote or remove it entirely.
  - Users cannot vote on movies they submitted themselves.

- **Movie Browsing**
  - Anyone (even unauthenticated visitors) can browse the full list of movies.
  - Each movie shows:
    - Title
    - Description
    - Submission date
    - Author (clickable to filter by that user)
    - Number of likes and hates

- **Sorting and Filtering**
  - Users can sort the list by:
    - Number of likes
    - Number of hates
    - Date of submission
  - Users can also filter the list by clicking on a username.

---

## Project Structure (High-level Overview)

/Controllers - Handles user requests and application flow   
/Services - Business logic (e.g., voting system)   
/Database - Database interaction layer   
/Database_Schema - MySQL script to set up the database   
/Views - Frontend pages (HTML)   
/Styles - Custom CSS for UI styling   


---

## Technologies Used

This project is built using the **XAMPP** stack:

- **Apache** (Web Server)
- **MySQL** (Database)
- **PHP** (Server-side logic)
- **HTML/CSS** (Frontend structure and styling)

---

## Installation Guide

1. Download and install **XAMPP**.
2. Start Apache and MySQL from the XAMPP Control Panel.
3. Open `phpMyAdmin` and create a new database (e.g., `movieworld`).
4. Import the SQL schema provided in the `Database_Schema` folder.
5. Place the project files inside the `htdocs` folder (e.g., `C:\xampp\htdocs\MovieWorld`).
6. Update the database connection credentials in the configuration files if necessary.
7. Open your browser and go to: `http://localhost/MovieWorld`
8. You can now register, log in, submit movies, and vote.

---

## Purpose

This project was developed as a prototype to demonstrate the design and implementation of a basic full-stack web application. It includes:

- User authentication
- CRUD functionality
- Relational database usage
- Secure voting system with restrictions
- Sorting and filtering mechanisms

---

Feel free to explore, test, and contribute.

