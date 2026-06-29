🛠️ 1. System Requirements (For your own server, copy "www" content to server directory)
Before proceeding with the installation, make sure your server meets the following minimum requirements:

- Web Server: Apache, Nginx, IIS, or equivalent.
- PHP Version: PHP 7.4 or higher (Recommended: PHP 8.x).
- Required PHP Extensions:
  - PDO (along with the corresponding driver: pdo_sqlite and/or pdo_mysql)
  - mbstring (for multibyte string handling)
  - openssl (for secure SMTP emails and API communications)
  - curl (for WhatsApp API gateway integrations)
  - gd (for logo and image processing)
- Database Engine:
  - SQLite (Recommended): Highly convenient. Stores all database tables locally inside a single secure file (src/rmagest.sqlite).
  - MySQL / MariaDB: Ideal for stable local area networks or shared cloud hosting servers.


🛠️ 1.1 System Requirements (For Windows users, using built-in PHP server)
- Windows system
- Administrative privileges

🚀 2. Installation and Configuration Guide
There are two possible paths to install and run RMA Gest: via the portable version (Windows) or on your own web server.

Option A: Portable Installation (Windows - Recommended for Local/Testing Use)
The application already includes a pre-configured integrated PHP web server with SQLite database drivers. No extra installation is required.

1 - Access the RMAGest folder.
2 - Double-click the Iniciar - Start.bat file. A console window will open, and your default web browser will automatically open http://localhost:8080/.
3 - Follow the guided web installer and select the SQLite (Recommended) database engine to complete the setup in seconds.
4 - Important: Do not close the console window while using the application. To stop the server safely, close the console window or double-click Parar - Stop.bat.

Option B: Installing on Your Own Web Server (Custom/Production)
If you prefer to host RMA Gest on your own environment (e.g. Linux cloud server, shared cPanel hosting, or locally using XAMPP/WAMP/Laragon):

1- Copy the Files: Copy all contents of the www folder to your web server's public directory (e.g., /var/www/html/ on Linux, public_html/ on cPanel, or htdocs/ in XAMPP).
2 - Access via Browser: Navigate to your server's URL in your web browser (e.g., http://your-domain.com/ or http://localhost/).
3 - Guided Installer: The system will detect the missing config.php file and redirect you to the guided web installer (install.php).
4 - Database Setup:
  - If using SQLite (Recommended): Select SQLite in the installer. The database file will be created at src/rmagest.sqlite. Ensure that the src directory has **write permissions** (e.g., chmod 775 or 777 on Linux) so PHP can read and write to the local database file.
  - If using MySQL/MariaDB: Create a clean database in your hosting panel (e.g. phpMyAdmin) and enter the connection details (Host, Port, User, Password, and Database Name) in the installer.

5 - Final Configurations: Complete the company details, upload your logo, configure SMTP mail settings, and create your administrator account. The installer will generate the config.php file automatically.

👥 3. Client Area (Public Portal)
The public portal allows customers to interact directly with the repair shop in an intuitive way.

Available Features:
- Search Repair by Code: Customers enter their tracking code (e.g. REP-2026-00001) in the Google-style search bar to instantly see their device's current stage.
- Online Repair Request (RMA Form): Customers fill out their contact details, describe the device symptoms, and optionally upload a photograph of the physical condition.
- Real-Time Tracking: An interactive timeline displays the repair progress (Under analysis, Under Diagnostics, Repaired, etc.).
- Budget View: Clients can see the estimated cost and the budget status (Pending Decision or Paid/Approved).
- Conversational Support: By entering the unique 6-digit access code sent to their email or WhatsApp, the client unlocks a secure chat with the technician to discuss budgets or upload files.

🛠️ 4. Technical Area (Technician Panel)
Navigate to "index.php?route=tech/login" and authenticate using your credentials to access the laboratory dashboard.

4.1. Dashboard Overview
Provides critical indicators in real-time:
- Quick KPIs: Count of active tickets in progress, tickets on hold waiting for client decision, repaired devices ready for pick-up, and inventory items with low stock.
- Advanced Filtering: Search repair sheets by RMA code, customer name, or device, and filter by statuses.

4.2. RMA Sheet Management
Opening any RMA ticket from the list provides the following tools:
- Update Status & Notify: When changing the status of a repair and adding a public comment, the system automatically dispatches an email or WhatsApp notification to the client.
- Private Diagnostic Notes: Write internal laboratory logs visible only to technical staff.
- Parts and Inventory Deductions: Add parts used during the repair. You can deduct parts directly from the global workshop stock or input them manually (setting quantity and price). The total cost updates automatically.
- Assistance Reports: Print a complete technical ticket to attach physically to the device or hand over to the customer.
- Direct Chat Feed: Real-time conversation channel to negotiate budgets and send pictures of damaged hardware.
- GDPR "Forget Customer": Permanently and irreversibly wipes the customer's personal data from the repair sheet to comply with privacy rules, retaining only the billing details.

4.3. Stock Control (Inventory)
Manage spare parts and workshop consumables (e.g. LCD Screens, Batteries, Solid State Drives):
- Register stock items with SKU references, available quantity, and sale unit prices.
- Triggers visual warnings when quantities fall below safety thresholds.

4.4. Reports & Statistics
Filter data by date ranges to analyze estimated invoice volumes, total billed (paid), and total pending budgets.

⚙️ 5. Global System Settings
Reserved for administrators to customize the core settings of the application:

1 - General: Workshop name, company logo image, and browser tab favicon.
2 - Email Settings (SMTP): Server settings for automated notifications.
3 - Workflow Statuses: Add, reorder, or delete custom steps displayed on the client's timeline.
4 - Device Types: Categories of equipment accepted in the laboratory.
5 - Users & Permissions: Add technical users and configure granular permissions (access to settings, inventory, reports, deleting tickets, etc.).
6 - WhatsApp Integrations: Configure your WhatsApp Gateway API endpoint, define auth tokens, and customize JSON payloads using {phone} and {message} variables.
7 - Message Templates: Customize notifications triggered by key events (new ticket submit, status update, chat message) with support for placeholders like {client_name}, {rma_number}, etc.
8 - Integration Code (Widget Generator): A visual builder to generate external search boxes. Copy the generated HTML/CSS block to place it on your corporate website, allowing customers to query their repair status directly from your homepage.
9 - Form Builder: Customize the public repair request form using a drag-and-drop interface, tailoring the collected data to your business model.
