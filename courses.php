<?php
require_once "admin_auth.php";
require_once "db.php";

// Fetch distinct programmes and levels for filters
$programmes = [];
$res_prog = $mysqli->query("SELECT DISTINCT programme FROM courses ORDER BY programme");
if($res_prog){
    while($row = $res_prog->fetch_assoc()){
        $programmes[] = $row['programme'];
    }
}

$levels = [];
$res_lvl = $mysqli->query("SELECT DISTINCT level FROM courses ORDER BY level");
if($res_lvl){
    while($row = $res_lvl->fetch_assoc()){
        $levels[] = $row['level'];
    }
}

// Handle search and filters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter_prog = isset($_GET['programme']) ? $_GET['programme'] : '';
$filter_lvl = isset($_GET['level']) ? $_GET['level'] : '';

// Build query
$query = "SELECT 
            course_code, 
            course_name, 
            COALESCE(credit_hours,0) AS credit_hours,
            COALESCE(lecturer_id,'N/A') AS lecturer_id,
            COALESCE(level,'N/A') AS level,
            COALESCE(programme,'N/A') AS programme,
            COALESCE(semester,'N/A') AS semester
          FROM courses
          WHERE 1=1";

if(!empty($search)){
    $search_safe = $mysqli->real_escape_string($search);
    $query .= " AND (course_name LIKE '%$search_safe%' OR course_code LIKE '%$search_safe%')";
}

if(!empty($filter_prog)){
    $filter_prog_safe = $mysqli->real_escape_string($filter_prog);
    $query .= " AND programme = '$filter_prog_safe'";
}

if(!empty($filter_lvl)){
    $filter_lvl_safe = (int)$filter_lvl;
    $query .= " AND level = $filter_lvl_safe";
}

$query .= " ORDER BY course_code AND level ASC ";

$res = $mysqli->query($query);
$courses = [];
if($res){
    while($row = $res->fetch_assoc()){
        $courses[] = $row;
    }
} else {
    echo "<p style='color:red;'>Query Error: " . $mysqli->error . "</p>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Courses</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="dashboardd.css">
<style>
/* Page-specific styles */
.main h2 {
    font-size: 22px;
    margin-bottom: 20px;
    font-weight: 600;
}

.filter-form {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 20px;
    align-items: center;
}

.filter-form input, .filter-form select {
    padding: 8px 12px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 14px;
}

.filter-form button {
    padding: 8px 15px;
    border: none;
    background: #3949ab;
    color: #fff;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    transition: 0.3s;
}

.filter-form button:hover {
    background: #5c6bc0;
}

.reset-btn {
    padding: 8px 15px;
    background: #ff7676;
    color: #fff;
    border-radius: 6px;
    text-decoration: none;
    font-size: 14px;
}

.reset-btn:hover {
    background: #ff4c4c;
}

.courses-table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

.courses-table thead {
    background: #1c2b4a;
    color: #fff;
}

.courses-table th, .courses-table td {
    padding: 12px 15px;
    font-size: 14px;
    text-align: left;
}

.courses-table tbody tr {
    border-bottom: 1px solid #eee;
    transition: background 0.2s;
}

.courses-table tbody tr:hover {
    background: #f1f1f1;
}

.no-courses {
    padding: 15px;
    color: #777;
    font-style: italic;
}
</style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">
        <h2>Admin Panel</h2>
    </div>
    <div class="sidebar-nav">
        <a href="dashboard.php">Dashboard</a>
        <a href="rg.php">Registered Courses</a>
        <a href="view_student.php">View Students</a>
        <a href="courses.php" class="active">Courses</a>
        <a href="logout.php" class="logout">Logout</a>
    </div>
</div>

<div class="main">
    <h2>Courses</h2>

    <!-- Filter and Search Form -->
    <form method="get" class="filter-form">
        <input type="text" name="search" placeholder="Search by course name or code..." value="<?php echo htmlspecialchars($search); ?>">

        <select name="programme">
            <option value="">All Programmes</option>
            <?php foreach($programmes as $prog): ?>
                <option value="<?php echo htmlspecialchars($prog); ?>" <?php if($prog==$filter_prog) echo 'selected'; ?>><?php echo htmlspecialchars($prog); ?></option>
            <?php endforeach; ?>
        </select>

        <select name="level">
            <option value="">All Levels</option>
            <?php foreach($levels as $lvl): ?>
                <option value="<?php echo htmlspecialchars($lvl); ?>" <?php if($lvl==$filter_lvl) echo 'selected'; ?>><?php echo htmlspecialchars($lvl); ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Filter</button>
        <a href="courses.php" class="reset-btn">Reset</a>
    </form>

    <!-- Courses Table -->
    <?php if(!empty($courses)): ?>
        <table class="courses-table">
            <thead>
                <tr>
                    <th>Course Code</th>
                    <th>Course Name</th>
                    <th>Programme</th>
                    <th>Level</th>
                    <th>Credit Hours</th>
                    <th>Semester</th>
                    <th>Lecturer ID</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($courses as $course): ?>
                <tr>
                    <td><?php echo htmlspecialchars($course['course_code']); ?></td>
                    <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                    <td><?php echo htmlspecialchars($course['programme']); ?></td>
                    <td><?php echo htmlspecialchars($course['level']); ?></td>
                    <td><?php echo $course['credit_hours']; ?></td>
                    <td><?php echo htmlspecialchars($course['semester']); ?></td>
                    <td><?php echo htmlspecialchars($course['lecturer_id']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="no-courses">No courses found.</p>
    <?php endif; ?>
</div>

</body>
</html>
