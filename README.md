Table of Contents
    Getting Started
    Requirements
    Installation
    Configuration
    Usage
    Features
    Troubleshooting
    Contributing
    License

Getting Started
    This project provides an audit tracking solution that monitors and logs various activities within an application, allowing easy tracking and reporting.

Requirements
    XAMPP - PHP and MySQL server
    Laravel - PHP framework
    Composer - Dependency management
    Blade - Templating engine used with Laravel

Installation
    Clone the Repository: git clone <https://github.com/levi0419/security-tool.git> then cd AUDIT-SECURITY.
    Install Dependencies: Run composer install and (if needed) npm install.
    Setup Environment: Copy .env.example to .env with cp .env.example .env.

Database Setup:
    Open phpMyAdmin (usually accessible via http://localhost/phpmyadmin).
    Create a new database named audit_system.
    In .env, set:
    env
    Copy code
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=audit_system
    DB_USERNAME=root
    DB_PASSWORD=

Run Migrations:
    Run php artisan migrate to create the database tables.
    Serve the Application: Start the Laravel development server using php artisan serve. Access your app at http://localhost:8000.


Usage
    Logging Audits: The system logs predefined activities, viewable at the /audit route.
    Audit Reports: Users can filter and view reports by date, user, or activity type.
    Report Filtering: Filter audits by date, user, or event.
    Notifications: Email or on-screen notifications for selected activities.
    Blade Templating: Responsive front-end UI built with Blade.
    Troubleshooting
    Database Connection Error: Ensure .env has correct DB credentials and that MySQL in XAMPP is running.
    Missing Dependencies: Run composer install or npm install.
    Migration Issues: Confirm audit_system database exists.
    
Contributing
Feel free to fork this repository, create feature branches, and submit pull requests.

