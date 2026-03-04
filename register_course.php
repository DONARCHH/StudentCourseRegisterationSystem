<?php
session_start();
if(!isset($_SESSION['student_id'])){
    header("Location: login.html");
    exit();
}
include "db.php";

$student_id = $_SESSION['student_id'];
$level = $_SESSION['level'];
$programme = $_SESSION['programme'];
$semester = $_POST['semester'] ?? '';

$courses = [];
$total_credit_hours = 0;
if($semester){
    $stmt = $conn->prepare("SELECT * FROM courses WHERE level=? AND programme=? AND semester=?");
    $stmt->bind_param("iss", $level, $programme, $semester);
    $stmt->execute();
    $result = $stmt->get_result();
    while($row = $result->fetch_assoc()){
        $courses[] = $row;
        $total_credit_hours += $row['credit_hours'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Course Registration</title>
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
            <a href="register_course.php" class="active">Course Registration</a>
            <a href="registered.php">Registered Courses</a>
            <a href="timetable.php">Timetable</a>
            <a href="logout.php" class="logout">Logout</a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="content">
        <header class="page-header">
            <h1>Course Registration</h1>
            <p>Select your semester to view available courses.</p>
        </header>

        <section class="card">

            <!-- Semester Selection -->
            <form method="POST" action="register_course.php" class="form-semester">
                <label for="semester">Select Semester:</label>
                <select name="semester" id="semester" onchange="this.form.submit()">
                    <option value="">-- Select Semester --</option>
                    <option value="First" <?php if($semester=='First') echo 'selected'; ?>>First</option>
                    <option value="Second" <?php if($semester=='Second') echo 'selected'; ?>>Second</option>
                </select>
            </form>

            <!-- Courses Table -->
            <?php if(!empty($courses)): ?>
            <form method="POST" action="preview_registration.php">
                <input type="hidden" name="semester" value="<?php echo htmlspecialchars($semester); ?>">
                <table class="courses-table">
                    <thead>
                        <tr>
                            <th>Select</th>
                            <th>Course Code</th>
                            <th>Course Name</th>
                            <th>Credit Hours</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($courses as $course): ?>
                        <tr>
                            <td><input type="checkbox" name="courses[]" value="<?php echo $course['course_code']; ?>"></td>
                            <td><?php echo $course['course_code']; ?></td>
                            <td><?php echo $course['course_name']; ?></td>
                            <td><?php echo $course['credit_hours']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <button type="submit" class="btn-primary">Preview Selected Courses</button>
            </form>
            <?php elseif($semester): ?>
                <p class="empty-msg">No courses available for <?php echo htmlspecialchars($semester); ?> semester.</p>
            <?php endif; ?>

        </section>
    </main>

</div>

</body>
</html>
