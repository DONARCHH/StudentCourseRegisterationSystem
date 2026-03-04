<?php
require_once "admin_auth.php";
require_once "db.php";

// Fetch distinct programmes for filter
$programmes = [];
$res = $mysqli->query("SELECT DISTINCT programme FROM students ORDER BY programme");
if ($res && $res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $programmes[] = $row['programme'];
    }
}

// Fetch distinct levels for filter
$levels = [];
$res = $mysqli->query("SELECT DISTINCT level FROM students ORDER BY level");
if ($res && $res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $levels[] = $row['level'];
    }
}

// Fetch distinct semesters for filter
$semesters = [];
$res = $mysqli->query("SELECT DISTINCT semester FROM registration ORDER BY semester");
if ($res && $res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $semesters[] = $row['semester'];
    }
}

// Fetch distinct courses for filter
$courses = [];
$res = $mysqli->query("SELECT DISTINCT course_name FROM courses ORDER BY course_name");
if ($res && $res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $courses[] = $row['course_name'];
    }
}

// Get selected filter values
$selected_programme = $_GET['programme'] ?? '';
$selected_level = $_GET['level'] ?? '';
$selected_semester = $_GET['semester'] ?? '';
$selected_course = $_GET['course'] ?? '';

// Fetch registered students with filters
$sql = "SELECT s.first_name, s.last_name, s.programme, s.level, c.course_name, r.semester
        FROM registration r
        JOIN students s ON r.student_id = s.student_id
        JOIN courses c ON r.course_code = c.course_code
        WHERE 1";

if ($selected_programme) {
    $sql .= " AND s.programme = '".$mysqli->real_escape_string($selected_programme)."'";
}
if ($selected_level) {
    $sql .= " AND s.level = '".$mysqli->real_escape_string($selected_level)."'";
}
if ($selected_semester) {
    $sql .= " AND r.semester = '".$mysqli->real_escape_string($selected_semester)."'";
}
if ($selected_course) {
    $sql .= " AND c.course_name = '".$mysqli->real_escape_string($selected_course)."'";
}

$sql .= " ORDER BY r.semester, s.programme, s.level, s.last_name";

$res = $mysqli->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Registered Students</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="dashboardd.css">
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">
        <h2>Admin Panel</h2>
    </div>
    <div class="sidebar-nav">
        <a href="dashboard.php">Dashboard</a>
        <a href="rg.php" class="active">Registered Courses</a>
        <a href="view_student.php">View Students</a>
        <a href="courses.php">Courses</a>
        <a href="logout.php" class="logout">Logout</a>
    </div>
</div>

<div class="main">
    <div class="topbar">
        <h2>Registered Students</h2>
        <strong>Admin</strong>
    </div>

    <!-- Filter Form -->
    <form method="get" class="filter-form">
        <select name="programme">
            <option value="">All Programmes</option>
            <?php foreach ($programmes as $prog): ?>
                <option value="<?= htmlspecialchars($prog) ?>" <?= ($selected_programme==$prog)?'selected':'' ?>>
                    <?= htmlspecialchars($prog) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="level">
            <option value="">All Levels</option>
            <?php foreach ($levels as $lvl): ?>
                <option value="<?= htmlspecialchars($lvl) ?>" <?= ($selected_level==$lvl)?'selected':'' ?>>
                    <?= htmlspecialchars($lvl) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="semester">
            <option value="">All Semesters</option>
            <?php foreach ($semesters as $sem): ?>
                <option value="<?= htmlspecialchars($sem) ?>" <?= ($selected_semester==$sem)?'selected':'' ?>>
                    <?= htmlspecialchars($sem) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="course">
            <option value="">All Courses</option>
            <?php foreach ($courses as $course): ?>
                <option value="<?= htmlspecialchars($course) ?>" <?= ($selected_course==$course)?'selected':'' ?>>
                    <?= htmlspecialchars($course) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Filter</button>
        <a href="rg.php" class="reset-btn">Reset</a>
    </form>

    <!-- Registered Students Table -->
    <table>
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Programme</th>
                <th>Level</th>
                <th>Course Registered</th>
                <th>Semester</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($res && $res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    $full_name = $row['first_name'] . ' ' . $row['last_name'];
                    echo "<tr>
                            <td>{$full_name}</td>
                            <td>{$row['programme']}</td>
                            <td>{$row['level']}</td>
                            <td>{$row['course_name']}</td>
                            <td>{$row['semester']}</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No registrations found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>
