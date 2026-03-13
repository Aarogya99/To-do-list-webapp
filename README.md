# To-Do List Web Application

A professional, responsive, and sleek To-Do List web application built using PHP, MySQL, and Vanilla CSS. Features a modern dark-mode UI with glassmorphism effects, data persistence, and full CRUD operations.

## Features

- **Add Tasks**: Quickly add new tasks to your list.
- **Mark as Completed**: Toggle task status between pending and completed.
- **Delete Tasks**: Remove tasks completely from the list.
- **Modern UI**: Clean, dynamic design with hover effects, custom checkboxes, and Boxicons.
- **Persistent Data**: Saves tasks seamlessly into a MySQL database via secure PDO connections.

## Tech Stack

- **Frontend**: HTML5, Vanilla CSS, Boxicons
- **Backend**: PHP 8.x
- **Database**: MySQL Server (PDO)

## Prerequisites

To run this application locally, you will need:
- A local web server stack like XAMPP, WAMP, or MAMP.
- PHP installed.
- MySQL database running.

## Installation & Setup

1. **Clone the repository:**
   Grab this project and place it inside your server's root directory (`htdocs` for XAMPP).
   ```bash
   git clone https://github.com/Aarogya99/To-do-list-webapp.git
   ```

2. **Set up the Database:**
   - Open MySQL (e.g., via phpMyAdmin).
   - Import the `database.sql` file provided in the repository to automatically create the database and table structure.

3. **Configure Database Connection:**
   - Open `config.php`.
   - Verify the database credentials (usually `root` for username and empty `''` for password on local servers like XAMPP):
     ```php
     $host = 'localhost';
     $dbname = 'todo_db';
     $username = 'root'; // Update if needed
     $password = ''; // Update if needed
     ```

4. **Launch Application:**
   Open your browser and navigate to:
   ```
   http://localhost/To-do-list-webapp/
   ```

## Author

**Aarogya99**
- GitHub: [@Aarogya99](https://github.com/Aarogya99)
