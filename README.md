# Professional To-Do List Web Application

A professional, responsive, and sleek To-Do List web application built using PHP, MySQL, and Vanilla CSS. Features a modern dark/light mode UI with glassmorphism effects, drag-and-drop reordering, dashboards, data persistence, and full CRUD operations.

![Demo](https://github.com/Aarogya99/To-do-list-webapp/assets/placeholder.gif) *Replace with actual screenshot/gif*

## ✨ Features

- **Advanced Task Management**: Create, edit, and delete tasks with detailed descriptions.
- **Subtasks & Checklists**: Break down large tasks into smaller, manageable subtasks directly from the list.
- **Priority & Categorization**: Assign Low, Medium, or High priorities. Organize tasks via categories (Personal, Work, Shopping, Health, Finance).
- **Due Dates & Due Animations**: Set due dates. Tasks strictly cannot be completed if their due date is in the future. Overdue pending tasks pulse with a red warning animation.
- **Drag & Drop Reordering**: Reorder tasks fluidly using Sortable.js; changes sync directly to the database.
- **Search & Filtering**: Instantly search tasks by title/description and filter by Active, Completed, or All.
- **Analytics Dashboard**: A dedicated page featuring real-time statistics and a beautifully animated Bar Chart (via Chart.js) tracking your tasks per category.
- **Activity Log Memory**: Keeps an audit log of all creations, updates, and deletions visible on your dashboard.
- **Web App Notifications**: Prompts browser-level desktop notifications if you have tasks due within the next 24 hours.
- **Modern UI & Themes**: Buttery smooth Dark/Light mode toggle (saved to `localStorage`), premium glassmorphism design, and satisfying hover state animations.

## 🛠 Tech Stack

- **Frontend**: HTML5, Vanilla CSS (CSS Variables & Flexbox/Grid), JavaScript, Boxicons, Chart.js, Sortable.js
- **Backend**: PHP 8.x
- **Database**: MySQL Server (PDO)

## 📋 Prerequisites

To run this application locally, you will need:
- A local web server stack like XAMPP, WAMP, or MAMP.
- PHP installed.
- MySQL database running.

## 🚀 Installation & Setup

1. **Clone the repository:**
   Grab this project and place it inside your server's root directory (`htdocs` for XAMPP).
   ```bash
   git clone https://github.com/Aarogya99/To-do-list-webapp.git
   ```

2. **Set up the Database:**
   - Open MySQL (e.g., via phpMyAdmin).
   - Create a database called `todo_db`.
   - Import the `database.sql` file provided in the repository.
   - *Note: If downloading the latest specific features, there may be an included `update_db.php` script you can run once to migrate the table schema with new columns (e.g. priority, categories, subtasks).*

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

## 🤝 Contributing
Contributions, issues, and feature requests are welcome!

## 👤 Author

**Aarogya99**
- GitHub: [@Aarogya99](https://github.com/Aarogya99)
