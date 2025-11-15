# E-BHM Connect: An Integrated ICT Solution

This project is an integrated web-based system designed to support the Barangay Health Workers (BHWs) of Barangay Bacong, Dumangas, Iloilo. It replaces manual, paper-based processes with a digital platform, improving efficiency, accuracy, and accessibility of community health data.

The system is built using **Vanilla PHP** and a manual routing system to ensure the code is clear, explainable, and free from complex framework dependencies.

## Features

The system is divided into three main components:

### 1. Public-Facing Site
* **Home Page:** General information about the health center.
* **Announcements:** Public health updates from BHWs.
* **Contact Page:** Lists BHS and nearest hospital contact info.
* **Anonymous Chatbot:** A public-facing AI chatbot (powered by the Gemini API) for general health inquiries.

### 2. Patient Portal (Secure)
* **Patient Login:** Secure login for registered residents.
* **View Records:** Patients can view their own (read-only) health records, including vitals and immunization history.
* **Chatbot with History:** Access to the AI chatbot, with the ability to view past conversation history.

### 3. BHW Admin Portal (Secure)
* **BHW Login:** Secure login for Barangay Health Workers.
* **Patient Management (CRUD):** Full capabilities to create, read, update, and delete patient records.
* **Health Program Monitoring:** Track and manage community health programs.
* **Medication & Supply Tracking:** A simple inventory system for the health center.
* **Report Generation:** Generate and export reports (e.g., as PDFs).

## Technology Stack

* **Backend:** Vanilla PHP 8.x
* **Database:** MySQL
* **Frontend:** HTML, Bootstrap 5 (via CDN)
* **JavaScript:** Native JavaScript (ES6+), SweetAlert2 (via CDN)
* **APIs:** Google Gemini API (for chatbot)
* **Server:** Apache (typically via XAMPP or WAMP)

## 🚀 Setup and Installation

Follow these steps to get the project running on your local machine.

### 1. Prerequisites
You must have a local server environment installed, such as:
* [XAMPP](https://www.apachefriends.org/index.html) (recommnded)
* WAMP
* MAMP

This provides Apache, MySQL, and PHP.

### 2. Clone the Repository
Clone this project into your server's `htdocs` (for XAMPP) or `www` (for WAMP) directory.

```bash
git clone [https://github.com/your-username/e-bhm-connect.git](https://github.com/your-username/e-bhm-connect.git)
Navigate into the new folder:

Bash

cd e-bhm-connect
3. Import the Database
Open your database management tool (e.g., phpMyAdmin from your XAMPP control panel).

Create a new, empty database named e-bhw_connect.

Select the e-bhw_connect database.

Click the Import tab.

Choose the database.sql file from the project's root folder and click Go. This will create all 10 tables.

4. Create Your Database Configuration File
This is the most important step. The database connection file is ignored by Git for security. You must create it manually.

Go to the config/ folder.

Create a new file named database.php.

Copy and paste the code below into your new database.php file:

PHP

<?php
/*
 * Database Connection Settings
 * This file is intentionally not tracked by Git.
 */

$host = '127.0.0.1';        // or 'localhost'
$db   = 'e-bhw_connect';   // The database name you created
$user = 'root';             // Your MySQL username (default for XAMPP)
$pass = '';                 // Your MySQL password (default for XAMPP is no password)
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     // If connection fails, stop the script and show the error
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>
Note: If your MySQL setup has a different password, update the $pass variable.

5. (Optional) Create API Config File
For the chatbot to work, you will need to create another config file for your API key.

In the config/ folder, create a file named config.php.

Paste the following inside, adding your own API key:

PHP

<?php
// Store API keys and other global settings here

define('GEMINI_API_KEY', 'YOUR_GOOGLE_AI_STUDIO_API_KEY_HERE');
?>
6. Run the Project
You're all set!

Make sure your Apache and MySQL services are running in XAMPP.

Open your web browser and go to: http://localhost/e-bhm-connect/

The public home page should now be visible.

Project Structure Explained
The project uses a simple, manual "router" (index.php) and a clean folder structure to separate concerns.

/ (root):

index.php: The main router. All web requests are directed here. It reads the URL and decides which page to load.

.htaccess: Invisibly rewrites all requests to index.php.

database.sql: The database schema.

/config/: Holds all our configuration and credentials.

database.php: (You create this) Connects to the database.

config.php: (You create this) Stores API keys.

/pages/: Contains all the "views" or pages of the site.

/admin/: Pages only BHWs can see (e.g., dashboard.php).

/portal/: Pages only logged-in patients can see.

/public/: Pages everyone can see (e.g., home.php).

/includes/: Reusable pieces of code.

header_public.php, footer_public.php: The public site's header/footer.

auth_bhw.php: A security script that checks if a BHW is logged in.

/actions/: Backend processing scripts. These files do not display HTML. They handle form submissions, login logic, and API calls (e.g., login_bhw_action.php).

/assets/: All frontend files (CSS, JS, images).

/lib/: For third-party PHP libraries that are manually added (like FPDF for PDF generation).