# Movie-world
Movie Sharing Website

This is a web application for sharing movies where an admin can upload movies via a secure admin dashboard, and regular users can browse and watch the movies uploaded by the admin.
Features

    Admin Panel:
        Secure login for admin
        Dashboard to upload new movies
        Manage existing movie uploads

    User Interface:
        Browse and watch movies uploaded by the admin
        Responsive and user-friendly design

Requirements
To run this project locally, ensure you have the following installed:
    XAMPP (or any PHP + MySQL local server environment)
    Web browser (Chrome, Firefox, etc.)

Setup Instructions;
Follow the steps below to run the project on your local machine:
    Install XAMPP
    Download and install XAMPP for your operating system from here.
    Configure Apache Port
    By default, this project uses port 3307 for the MySQL server.
    Make sure to start MySQL service in XAMPP and confirm port 3307 is being used. You may need to configure the port in XAMPP settings if it differs.

    Create the Database
        Open phpMyAdmin via XAMPP (usually http://localhost/phpmyadmin).
        Create a new database.
        Import the database structure and tables using the SQL commands inside the database.txt just above see it file provided in this repo.

    Place the Project Files
        Copy the project folder to the XAMPP htdocs directory (e.g., /opt/lampp/htdocs on Linux or C:\xampp\htdocs on Windows).
        Ensure file permissions are set correctly to allow Apache to serve the files.

    Access the Website
    Open your browser and navigate to:

http://localhost/YourProjectFolder/

Admin Login & Upload Movies
    Access the admin panel from:
        http://localhost/YourProjectFolder/dashboard.php
        Use the admin credentials provided (you can specify default username/password here if applicable).
        Upload movies using the dashboard interface.

Notes
    Make sure PHP and MySQL services are running in XAMPP before accessing the website.
    The database credentials (username, password, host, port) are configured in the project files — update them if needed.
    The project is for learning/demo purposes; please secure it properly before production use.
Contact
For any questions or contributions, feel free to reach out via anmolghimire286@gmail.com or open an issue on this repository.
