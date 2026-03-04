<?php
session_start();
if(!isset($_SESSION['student_id'])){
    header("Location: login.html");
    exit();
}
include "db.php";

$student_id = $_SESSION['student_id'];
$semester = $_POST['semester'] ?? '';
$selected_courses = $_POST['courses'] ?? [];

$registered_courses = [];
$total_credit_hours = 0;

if(!empty($selected_courses)){
    // Fetch course details from DB
    $placeholders = implode(',', array_fill(0, count($selected_courses), '?'));
    $stmt = $conn->prepare("SELECT course_code, course_name, credit_hours FROM courses WHERE course_code IN ($placeholders)");
    $types = str_repeat('s', count($selected_courses));
    $stmt->bind_param($types, ...$selected_courses);
    $stmt->execute();
    $result = $stmt->get_result();

    while($row = $result->fetch_assoc()){
        $registered_courses[] = $row;
        $total_credit_hours += $row['credit_hours'];
    }
    $stmt->close();

    // Insert into registration table
    $stmt = $conn->prepare("INSERT INTO registration (course_code, semester, date_registered, student_id) VALUES (?, ?, NOW(), ?)");
    foreach($selected_courses as $code){
        $stmt->bind_param("ssi", $code, $semester, $student_id);
        $stmt->execute();
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Course Registration Confirmation</title>
    <link rel="stylesheet" href="register_course.css">
</head>l
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
            <h1>Course Registration Confirmation</h1>
            <?php if($semester): ?>
            <p>Semester: <strong><?php echo htmlspecialchars($semester); ?></strong></p>
            <?php endif; ?>
        </header>

        <?php if(!empty($registered_courses)): ?>
            <table class="courses-table">
                <thead>
                    <tr>
                        <th>Course Code</th>
                        <th>Course Name</th>
                        <th>Credit Hours</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($registered_courses as $course): ?>
                        <tr>
                            <td><?php echo $course['course_code']; ?></td>
                            <td><?php echo $course['course_name']; ?></td>
                            <td><?php echo $course['credit_hours']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="total-row">
                        <td colspan="2" style="text-align:right; font-weight:bold;">Total Credit Hours:</td>
                        <td style="font-weight:bold;"><?php echo $total_credit_hours; ?></td>
                    </tr>
                </tbody>
            </table>

            <button onclick="window.print()" class="btn-primary">Print / Save as PDF</button>
        <?php else: ?>
            <p class="empty-msg">No courses were selected or registered.</p>
            <a href="register_course.php">Go Back to Register Courses</a>
        <?php endif; ?>

        <br><br>
        <a href="dashboard.php">Back to Dashboard</a>
    </main>

</div>
</body>
</html>
