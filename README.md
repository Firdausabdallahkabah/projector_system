PROJECTOR RESERVATION AND MANAGEMENT SYSTEM

This web-based application allows users to register, log in, and manage projector reservations for academic or institutional use. It is built with PHP, HTML, CSS, and JavaScript, and designed to streamline projector bookings and ensure organized usage tracking.

🚀 FEATURES

- ✅ User Registration & Login
- ✅ Secure Authentication
- ✅ Projector Booking Interface
- ✅ View & Manage Reservations
- ✅ Admin-Level Management
- ✅ Logout Functionality
- ✅ Clean UI with Responsive Design

📁 PROJECT STRUCTURE

projector_system/
├── api/                    # Backend endpoints for AJAX/API calls
├── css/                    # Stylesheets
├── js/                     # JavaScript files for UI behavior
├── includes/               # Reusable PHP components (e.g., DB connection)
├── index.php               # Homepage/dashboard
├── login.php               # User login form
├── register.php            # New user registration
├── logout.php              # Ends user session
├── manage_projectors.php   # Main reservation/management page

⚙️ TECHNOLOGIES USED

- PHP (Backend)
- MySQL (Database – configure in includes/db.php)
- HTML/CSS (Frontend structure and styling)
- JavaScript (Basic frontend interactivity)

🛠️ SETUP INSTRUCTIONS

1. CLONE THE REPOSITORY
   git clone https://github.com/Firdausabdallahkabah/projector_system.git

2. MOVE TO PROJECT DIRECTORY
   cd projector_system

3. SET UP LOCAL SERVER
   - Use XAMPP, WAMP, or any PHP server.
   - Place the project in your server root (e.g., htdocs for XAMPP).

4. CONFIGURE THE DATABASE
   - Create a MySQL database (e.g., projector_system)
   - Import SQL schema if provided (you may need to manually create tables).
   - Update database credentials in includes/db.php.

5. RUN THE APP
   Open your browser and navigate to:
   http://localhost/projector_system/index.php

 AUTHORS

Group E


📜 LICENSE

This project is open-source and free to use for educational or non-commercial purposes.
