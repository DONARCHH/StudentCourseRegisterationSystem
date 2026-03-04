<?php
require_once "admin_auth.php";
require_once "db.php";

// Fetch distinct programmes
$programmes = [];
$res_prog = $mysqli->query("SELECT DISTINCT programme FROM students ORDER BY programme");
if($res_prog){
    while($row = $res_prog->fetch_assoc()){
        $programmes[] = $row['programme'];
    }
}

// Fetch distinct levels
$levels = [];
$res_lvl = $mysqli->query("SELECT DISTINCT level FROM students ORDER BY level");
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
$query = "SELECT student_id, COALESCE(first_name,'') AS first_name, COALESCE(last_name,'') AS last_name,
                 COALESCE(programme,'N/A') AS programme, COALESCE(level,'N/A') AS level, 
                 COALESCE(phone,'N/A') AS phone
          FROM students WHERE 1=1";

if(!empty($search)){
    $search_safe = $mysqli->real_escape_string($search);
    $query .= " AND (first_name LIKE '%$search_safe%' OR last_name LIKE '%$search_safe%' OR programme LIKE '%$search_safe%')";
}

if(!empty($filter_prog)){
    $filter_prog_safe = $mysqli->real_escape_string($filter_prog);
    $query .= " AND programme = '$filter_prog_safe'";
}

if(!empty($filter_lvl)){
    $filter_lvl_safe = $mysqli->real_escape_string($filter_lvl);
    $query .= " AND level = '$filter_lvl_safe'";
}

$query .= " ORDER BY student_id ASC";

$res = $mysqli->query($query);
$students = [];
if($res){
    while($row = $res->fetch_assoc()){
        $students[] = $row;
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
<title>View Students</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="dashboardd.css">
<style>
/* Page-specific button styling and alignment */
.btn {
    display: inline-block;
    padding: 6px 12px; /* uniform height/width */
    border-radius: 5px;
    font-size: 13px;
    text-decoration: none;
    text-align: center;
    line-height: 1; /* vertical alignment fix */
    transition: 0.2s;
}

.add-btn { background: #28a745; color: #fff; }
.add-btn:hover { background: #218838; }

.edit-btn { background: #ffc107; color: #000; }
.edit-btn:hover { background: #e0a800; }

.delete-btn { background: #dc3545; color: #fff; }
.delete-btn:hover { background: #c82333; }

/* Actions cell: horizontal alignment */
.actions-cell {
    display: flex;
    gap: 6px; /* spacing between buttons */
    justify-content: flex-start; /* left align inside cell */
    align-items: center; /* vertical center */
}

/* Top Add button container */
.add-btn-container {
    display: flex;
    justify-content: flex-end; /* push button to right */
    margin-bottom: 15px;
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
        <a href="view_students.php" class="active">View Students</a>
        <a href="courses.php">Courses</a>
        <a href="logout.php" class="logout">Logout</a>
    </div>
</div>

<div class="main">
    <h2>View Students</h2>

    <!-- Add Student Button -->
    <div class="add-btn-container">
        <a href="add_student.php" class="btn add-btn">Add Student</a>
    </div>

    <!-- Filter and Search Form -->
    <form method="get" class="filter-form">
        <input type="text" name="search" placeholder="Search by name or programme..." value="<?php echo htmlspecialchars($search); ?>">
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
        <a href="view_student.php" class="reset-btn">Reset</a>
    </form>

    <!-- Students Table -->
    <?php if(!empty($students)): ?>
        <table class="students-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Student Name</th>
                    <th>Programme</th>
                    <th>Level</th>
                    <th>Phone</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($students as $student): 
                    $full_name = $student['first_name'] . ' ' . $student['last_name'];
                ?>
                <tr>
                    <td><?php echo $student['student_id']; ?></td>
                    <td><?php echo htmlspecialchars($full_name); ?></td>
                    <td><?php echo htmlspecialchars($student['programme']); ?></td>
                    <td><?php echo htmlspecialchars($student['level']); ?></td>
                    <td><?php echo htmlspecialchars($student['phone']); ?></td>
                    <td class="actions-cell">
                        <a href="edit_student.php?id=<?php echo $student['student_id']; ?>" class="btn edit-btn">Edit</a>
                        <a href="delete_student.php?id=<?php echo $student['student_id']; ?>" class="btn delete-btn" onclick="return confirm('Are you sure you want to delete this student?');">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="no-students">No students found.</p>
    <?php endif; ?>
</div>

</body>
</html>
