RealStateCRM
RealStateCRM is a real estate customer relationship management system built with Laravel. It helps manage leads, agents, supervisors, projects, units, deals, and other CRM operations.
Requirements
Before installing the project, make sure the following are installed:
•	PHP 8.4 or higher
•	Composer
•	MySQL 8.0 or higher
•	Node.js 22 or higher
•	npm
•	Git
You can use XAMPP or another local MySQL server for development.
Installation
1. Clone the repository
git clone https://github.com/Natnael-Bacha/NovaCrm.git
cd NovaCrm
2. Install PHP dependencies
composer install
3. Install JavaScript dependencies
npm install
Environment Configuration
Create your environment file by copying .env.example:
Windows
copy .env.example .env
macOS / Linux
cp .env.example .env
Generate the Laravel application key:
php artisan key:generate

Database Setup
NovaCrm uses MySQL.
1. Start MySQL
If you are using XAMPP, open XAMPP Control Panel and start MySQL.
2. Create the database
Create a MySQL database named:
realstate_crm
You can create it using phpMyAdmin or the MySQL command line.
3. Configure .env
Update the database section of your .env file according to your local MySQL configuration:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=realstate_crm
DB_USERNAME=root
DB_PASSWORD=
4. Run migrations
After configuring the database, run:
php artisan migrate
If the project contains database seeders and you want to populate the database with initial data:
php artisan db:seed




Build Frontend Assets
The project uses Vite for frontend assets.
To create a production build:
npm run build
Make sure this command completes successfully without errors.
For development, use:
npm run dev
Keep the Vite development server running while developing the application.

Running the Application
Start the Laravel development server:
php artisan serve
The application will normally be available at:
http://127.0.0.1:8000
When developing, you will normally need two terminal windows:
Terminal 1 — Laravel
php artisan serve
Terminal 2 — Vite
npm run dev
Make sure your MySQL server is also running.




Clear Laravel Cache
If you encounter configuration, route, view, or cache-related issues, run:
php artisan optimize:clear
Then restart the Laravel server.

Fresh Database Setup
To completely rebuild the database from the migrations, use:
php artisan migrate:fresh
If seed data is available:
php artisan migrate:fresh --seed
Warning: migrate:fresh deletes all existing tables and data in the configured database.
Production Build Check
Before submitting or deploying the project, verify that the application can be built successfully:
composer install
npm install
npm run build
php artisan optimize:clear
php artisan migrate
Then run:
php artisan serve
and verify that the application loads correctly.
Common Issues
MySQL connection error
If Laravel cannot connect to MySQL, check:
•	MySQL is running.
•	The database realstate_crm exists.
•	The database credentials in .env are correct.
•	MySQL is running on port 3306 or the port specified in .env.
After changing .env, run:
php artisan optimize:clear
Vite or frontend assets are not loading
Run:
npm install
npm run build
For development:
npm run dev
Laravel application key error
Run:
php artisan key:generate
Composer dependency errors
Make sure the required PHP version is installed and then run:
composer install
Application behaving unexpectedly after configuration changes
Run:
php artisan optimize:clear

Development Checklist
Before considering the project ready, verify:
•	 MySQL server is running
•	 realstate_crm database exists
•	 .env is configured
•	 composer install completes successfully
•	 npm install completes successfully
•	 php artisan migrate completes successfully
•	 npm run build completes successfully
•	 php artisan serve starts successfully
•	 Application opens in the browser
•	 Login/admin functionality works
•	 CRM pages load correctly
•	 Database operations work correctly

Important
Do not commit your .env file or any passwords, API keys, or other secrets to the repository.


