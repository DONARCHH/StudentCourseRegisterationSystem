<?php
require_once "admin_auth.php";
require_once "db.php";

// Fetch distinct programmes and levels for dropdowns
$programmes = [];
$res_prog = $mysqli->query("SELECT DISTINCT programme FROM students ORDER BY programme");
if($res_prog){
    while($row = $res_prog->fetch_assoc()){
        $programmes[] = $row['programme'];
    }
}

$levels = [];
$res_lvl = $mysqli->query("SELECT DISTINCT level FROM students ORDER BY level");
if($res_lvl){
    while($row = $res_lvl->fetch_assoc()){
        $levels[] = $row['level'];
    }
}

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $stmt = $mysqli->prepare("INSERT INTO students (first_name, last_name, phone, level, programme, department) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "ssssss", 
        $_POST['first_name'], 
        $_POST['last_name'], 
        $_POST['phone'], 
        $_POST['level'], 
        $_POST['programme'], 
        $_POST['department']
    );
    $stmt->execute();

    // Set flash message
    session_start();
    $_SESSION['flash_message'] = "Student added successfully.";

    header("Location: view_students.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Student</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="dashboardd.css">
<style>
/* Centered card */
.main {
    display: flex;
    justify-content: center;
    align-items: flex-start;
    padding: 40px 20px;
}
.card {
    background: #fff;
    padding: 30px 25px;
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    width: 100%;
    max-width: 500px;
}
.card h2 {
    margin-bottom: 20px;
    font-weight: 600;
    text-align: center;
}

/* Form elements */
.card form {
    display: flex;
    flex-direction: column;
    gap: 15px;
}
.card label {
    font-weight: 500;
    margin-bottom: 5px;
}
.card input, .card select {
    padding: 10px 12px;
    font-size: 14px;
    border-radius: 6px;
    border: 1px solid #ccc;
    width: 100%;
    box-sizing: border-box;
}
.card button {
    padding: 10px 15px;
    font-size: 15px;
    border-radius: 6px;
    border: none;
    background: #3949ab;
    color: #fff;
    cursor: pointer;
    transition: 0.3s;
}
.card button:hover {
    background: #5c6bc0;
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
        <a href="courses.php">Courses</a>
        <a href="logout.php" class="logout">Logout</a>
    </div>
</div>

<div class="main">
    <div class="card">
        <h2>Add New Student</h2>
        <form method="post">
            <div>
                <label>First Name:</label>
                <input type="text" name="first_name" required>
            </div>
            <div>
                <label>Last Name:</label>
                <input type="text" name="last_name" required>
            </div>
            <div>
                <label>Phone:</label>
                <input type="text" name="phone">
            </div>
            <div>
                <label>Level:</label>
                <select name="level" required>
                    <option value="">Select Level</option>
                    <?php foreach($levels as $lvl): ?>
                        <option value="<?php echo htmlspecialchars($lvl); ?>"><?php echo htmlspecialchars($lvl); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label>Programme:</label>
                <select name="programme" required>
                    <option value="">Select Programme</option>
                    <?php foreach($programmes as $prog): ?>
                        <option value="<?php echo htmlspecialchars($prog); ?>"><?php echo htmlspecialchars($prog); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label>Department:</label>
                <input type="text" name="department" required>
            </div>
            <button type="submit">Add Student</button>
        </form>
    </div>
</div>

</body>
</html>
