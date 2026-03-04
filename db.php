<?php
$host = "localhost";
$user = "root";        
$pass = "ar3H$!69";            
$dbname = "student_course_registration_portal";

$mysqli = new mysqli($host, $user, $pass, $dbname);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
?>
