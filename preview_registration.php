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

if(empty($selected_courses)){
    ?>
    <section class="card" style="text-align:center; max-width:500px; margin:50px auto; padding:30px;">
        <p style="color:red; font-weight:bold; font-size:1.1rem; margin-bottom:20px;">
            No courses selected.
        </p>
        <a href="register_course.php" class="btn-primary" style="padding:10px 25px; text-decoration:none;">
            Go Back to Register Courses
        </a>
    </section>
    <?php
    exit();
}

// Fetch course details from DB
$placeholders = implode(',', array_fill(0, count($selected_courses), '?'));
$stmt = $conn->prepare("SELECT course_code, course_name, credit_hours FROM courses WHERE course_code IN ($placeholders)");
$types = str_repeat('s', count($selected_courses));
$stmt->bind_param($types, ...$selected_courses);
$stmt->execute();
$result = $stmt->get_result();

$courses = [];
$total_credit_hours = 0;
while($row = $result->fetch_assoc()){
    $courses[] = $row;
    $total_credit_hours += $row['credit_hours'];
}

// Check if student already registered for this semester
$stmt_check = $conn->prepare("SELECT COUNT(*) AS count FROM registration WHERE student_id=? AND semester=?");
$stmt_check->bind_param("is", $student_id, $semester);
$stmt_check->execute();
$result_check = $stmt_check->get_result();
$row_check = $result_check->fetch_assoc();
$already_registered = $row_check['count'] > 0;
$stmt_check->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Preview Registration</title>
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
            <a href="registered.php">Registered Courses</a>
            <a href="timetable.php">Timetable</a>
            <a href="logout.php" class="logout">Logout</a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="content">
        <header class="page-header">
            <h1>Preview Selected Courses</h1>
            <p>Semester: <?php echo htmlspecialchars($semester); ?></p>
        </header>

        <section class="card">

            <table class="courses-table">
                <thead>
                    <tr>
                        <th>Course Code</th>
                        <th>Course Name</th>
                        <th>Credit Hours</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($courses as $c): ?>
                        <tr>
                            <td><?php echo $c['course_code']; ?></td>
                            <td><?php echo $c['course_name']; ?></td>
                            <td><?php echo $c['credit_hours']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="total-row">
                        <td colspan="2" style="text-align:right; font-weight:bold;">Total Credit Hours:</td>
                        <td style="font-weight:bold;"><?php echo $total_credit_hours; ?></td>
                    </tr>
                </tbody>
            </table>

         <?php if($already_registered): ?>
    <p style="color:red; font-weight:bold; margin-top:15px;">
        You have already registered courses for <?php echo htmlspecialchars($semester); ?> semester.
    </p>
    <div style="margin-top:10px;">
        <button onclick="window.location.href='registered.php'" class="btn-primary" style="padding:8px 20px; margin-right:10px;">
            View Registered Courses
        </button>
        <button onclick="window.location.href='register_course.php'" class="btn-primary" style="padding:8px 20px; background:#3949ab;">
            Go Back
        </button>
    </div>
<?php else: ?>
    <form method="POST" action="registered_courses.php" style="margin-top:20px;">
        <input type="hidden" name="semester" value="<?php echo htmlspecialchars($semester); ?>">
        <?php foreach($selected_courses as $code): ?>
            <input type="hidden" name="courses[]" value="<?php echo $code; ?>">
        <?php endforeach; ?>
        <button type="submit" class="btn-primary">Confirm Registration</button>
        <button type="button" onclick="window.print()" class="btn-primary" style="background:#3949ab; margin-left:10px;">Print / Save PDF</button>
    </form>
<?php endif; ?>


        </section>
    </main>

</div>

</body>
</html>
