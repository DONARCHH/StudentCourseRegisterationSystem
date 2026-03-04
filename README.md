Student Course Registration System
Overview

The Course Registration System is a web-based application developed to simplify and digitize the course enrollment process in an academic environment. It provides separate access for students and administrators, each with their own dashboard and functionalities.

Students can register for courses, view their timetable, and manage their academic selections through a personalized portal. Administrators can manage courses, monitor registrations, and control system data through an admin dashboard.

This project demonstrates practical knowledge in full-stack web development, database design, authentication systems, and data management.

Features
Authentication System
Secure login system for both students and administrators
Role-based access control
Separate dashboards for students and admin users
Student Functionalities
Register for available courses
View registered courses
View personalized timetable
Search and filter available courses
Dashboard overview of academic information

Administrator Functionalities
- Add new courses
- Update course details
- Delete courses
- View registered students
- Search and filter course or student records
- Dashboard for system monitoring

Technologies Used
- HTML
- CSS
- JavaScript
- PHP
- MySQL

System Architecture
The system follows a structured client-server architecture:
Frontend: Handles user interface and user interactions.
Backend: Processes authentication, form submissions, and business logic using PHP.
Database: Stores student accounts, admin accounts, course details, and registration records using MySQL.

Installation Guide
Clone the repository:

git clone https://github.com/DONARCHH/StudentCourseRegistrationSystem.git

Move the project folder into your local server directory (e.g., htdocs if using XAMPP).

Import the provided .sql database file into MySQL using phpMyAdmin.

Configure your database connection file by updating:

host
username
password
database_name

Start Apache and MySQL from your local development environment.

Open your browser and navigate to:
http://localhost/StudentCourseRegistrationSystem

How the System Works
Users log in based on their role (Student or Administrator).
After authentication, each user is redirected to their respective dashboard.
Students can search, filter, and register for courses.
Registered courses are stored in the database and reflected in the student’s timetable.
Administrators manage course records and monitor student registrations.

Future Improvements
- Password reset functionality
- Email notifications for registration confirmation
- GPA calculation and academic performance tracking
- Multi-semester management
- Deployment to a live production server

Author
This project was developed as part of a personal project to demonstrate understanding of web application development, authentication systems, and database management.
