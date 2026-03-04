<?php
session_start();
if(!isset($_SESSION['student_id'])){
    header("Location: login.html");
    exit();
}
include "db.php";

$student_id = $_SESSION['student_id'];

// Get registered courses
$stmt = $conn->prepare("
    SELECT r.semester, c.course_code, c.course_name, c.credit_hours
    FROM registration r
    JOIN courses c ON r.course_code = c.course_code
    WHERE r.student_id = ?
    ORDER BY r.semester, c.course_code
");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

$registered_courses = [];
$total_credit_hours = 0;

while($row = $result->fetch_assoc()){
    $registered_courses[] = $row;
    $total_credit_hours += $row['credit_hours'];
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registered Courses</title>
    <link rel="stylesheet" href="register_course.css">
</head>
<body>

<div class="layout">

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>Student Portal</h2>
        </div>
        <nav class="sidebar-nav">
            <a href="dashboard.php">Dashboard</a>
            <a href="register_course.php">Course Registration</a>
            <a href="registered.php" class="active">Registered Courses</a>
            <a href="timetable.php">Timetable</a>
            <a href="logout.php" class="logout">Logout</a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="content">
        <header class="page-header">
            <h1>Your Registered Courses</h1>
        </header>

        <?php if(!empty($registered_courses)): ?>
            <table class="courses-table">
                <thead>
                    <tr>
                        <th>Semester</th>
                        <th>Course Code</th>
                        <th>Course Name</th>
                        <th>Credit Hours</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($registered_courses as $c): ?>
                        <tr>
                            <td><?php echo $c['semester']; ?></td>
                            <td><?php echo $c['course_code']; ?></td>
                            <td><?php echo $c['course_name']; ?></td>
                            <td><?php echo $c['credit_hours']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="total-row">
                        <td colspan="3" style="text-align:right; font-weight:bold;">Total Credit Hours:</td>
                        <td style="font-weight:bold;"><?php echo $total_credit_hours; ?></td>
                    </tr>
                </tbody>
            </table>

            <button onclick="window.print()" class="btn-primary" style="margin-top:15px;">Print / Save as PDF</button>

        <?php else: ?>
            <p>You have not registered any courses yet.</p>
            <a href="register_course.php">Register Courses Now</a>
        <?php endif; ?>

        <br><br>
        <a href="dashboard.php">Back to Dashboard</a>
    </main>

</div>

</body>
</html>
