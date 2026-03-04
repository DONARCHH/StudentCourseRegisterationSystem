<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];
$stmt = $conn->prepare("SELECT programme, level FROM students WHERE student_id = ?");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

$programme = $student['programme'];
$level = $student['level'];

$query = "
    SELECT t.*, c.course_name, c.semester
    FROM timetable t
    JOIN courses c ON t.course_code = c.course_code
    WHERE t.programme = ? AND t.level = ?
    ORDER BY FIELD(t.day,'Monday','Tuesday','Wednesday','Thursday','Friday'), t.start_time
";
$stmt2 = $conn->prepare($query);
$stmt2->bind_param("si", $programme, $level);
$stmt2->execute();
$timetable = $stmt2->get_result();

$days = ['Monday','Tuesday','Wednesday','Thursday','Friday'];
$dayGrid = [];
while($row = $timetable->fetch_assoc()){
    $dayGrid[$row['day']][] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Timetable</title>
<style>

.sidebar {
    width: 250px;
    background: #1c2b4a;
    color: #fff;
    padding: 20px;
    height: 100vh;
    position: fixed;
    left: 0;
    top: 0;
    box-shadow: 3px 0 10px rgba(0,0,0,0.15);
}

.sidebar-header h2 {
    font-size: 20px;
    margin-bottom: 30px;
    font-weight: 600;
}

.sidebar-nav {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.sidebar-nav a {
    padding: 12px 15px;
    text-decoration: none;
    color: #c5cae9;
    font-size: 14px;
    border-radius: 6px;
    transition: 0.3s;
}

.sidebar-nav a:hover {
    background: #3949ab;
    color: white;
}

.sidebar-nav a.active {
    background: #5c6bc0;
    color: white;
}

.sidebar-nav a.logout {
    color: #ff7676 !important;
}

.main {
    margin-left: 270px; 
    padding: 30px;
}
.header {text-align:center; margin-bottom:25px;}
.header h2 {color:#1c2b4a;}
.details {
    display:flex;
    justify-content:center;
    gap:30px;
    margin-bottom:30px;
    font-size:16px;
    color:#333;
    flex-wrap: wrap;
}


.day-section {margin-bottom:25px;}
.day-title {
    font-size:20px;
    font-weight:600;
    margin-bottom:10px;
    color:#3949ab;
    border-bottom:2px solid #3949ab;
    padding-bottom:5px;
}
.card {
    background:#fff;
    padding:15px 20px;
    border-radius:10px;
    box-shadow:0 5px 15px rgba(0,0,0,0.08);
    margin-bottom:10px;
    transition: transform 0.2s, box-shadow 0.2s;
}
.card:hover {
    transform: translateY(-3px);
    box-shadow:0 10px 20px rgba(0,0,0,0.12);
}
.card h4 {margin:0 0 5px 0; color:#1c2b4a;}
.card p {margin:3px 0; color:#555; font-size:14px;}
.empty {text-align:center; color:#999; padding:20px; font-size:16px;}

@media(max-width:768px){
    .sidebar {width:60px; padding-top:15px;}
    .sidebar-nav a {padding:10px 5px; font-size:12px; text-align:center;}
    .main {margin-left:70px; padding:15px;}
    .details{flex-direction:column; align-items:center;}
}
</style>
</head>
<body>

<aside class="sidebar">
    <div class="sidebar-header">
        <h2>Student Portal</h2>
    </div>

    <nav class="sidebar-nav">
        <a href="dashboard.php">Dashboard</a>
        <a href="register_course.php">Course Registration</a>
        <a href="registered.php">Registered Courses</a>
        <a href="timetable.php" class="active">Timetable</a>
        <a href="logout.php" class="logout">Logout</a>
    </nav>
</aside>

<div class="main">
    <div class="header"><h2>Student Timetable</h2></div>
    <div class="details">
        <div><strong>Programme:</strong> <?= $programme; ?></div>
        <div><strong>Level:</strong> <?= $level; ?></div>
    </div>

    <?php foreach($days as $day): ?>
        <div class="day-section">
            <div class="day-title"><?= $day ?></div>
            <?php if(isset($dayGrid[$day])): ?>
                <?php foreach($dayGrid[$day] as $course): ?>
                    <div class="card">
                        <h4><?= $course['course_code'] ?> - <?= $course['course_name'] ?></h4>
                        <p><strong>Time:</strong> <?= date("H:i",strtotime($course['start_time'])) ?> - <?= date("H:i",strtotime($course['end_time'])) ?></p>
                        <p><strong>Semester:</strong> <?= $course['semester'] ?></p>
                        <p><strong>Venue:</strong> <?= $course['venue'] ?></p>
                        <p><strong>Lecturer:</strong> <?= $course['lecturer_name'] ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty">No classes scheduled.</div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>
