<?php
require_once "admin_auth.php";
require_once "db.php";

if(!isset($_GET['id'])){
    header("Location: view_students.php");
    exit();
}

$id = $_GET['id'];

// Fetch student info
$res = $mysqli->query("SELECT * FROM students WHERE student_id='$id'");
if($res && $res->num_rows > 0){
    $student = $res->fetch_assoc();
} else {
    echo "Student not found.";
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(isset($_POST['confirm']) && $_POST['confirm'] == 'yes'){
        $stmt = $mysqli->prepare("DELETE FROM students WHERE student_id=?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        
        // Set flash message
        session_start();
        $_SESSION['flash_message'] = "Student deleted successfully.";
    }
    header("Location: view_student.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Delete Student</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
body {
    margin: 0;
    font-family: 'Poppins', sans-serif;
    background: #f4f6f9;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
}

.delete-card {
    background: #fff;
    padding: 30px 40px;
    border-radius: 12px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.1);
    width: 100%;
    max-width: 450px;
    text-align: center;
}

.delete-card h2 {
    color: #dc3545;
    margin-bottom: 20px;
}

.delete-card p {
    font-size: 16px;
    margin-bottom: 30px;
}

.btn {
    padding: 12px 20px;
    border-radius: 8px;
    border: none;
    font-size: 14px;
    cursor: pointer;
    margin: 0 5px;
    transition: 0.3s;
    text-decoration: none;
    display: inline-block;
}

.cancel-btn {
    background: #6c757d;
    color: #fff;
}

.cancel-btn:hover {
    background: #5a6268;
}

.delete-btn {
    background: #dc3545;
    color: #fff;
}

.delete-btn:hover {
    background: #c82333;
}
</style>
</head>
<body>

<div class="delete-card">
    <h2>Delete Student</h2>
    <p>Are you sure you want to delete <strong><?php echo htmlspecialchars($student['first_name'].' '.$student['last_name']); ?></strong>?</p>
    
    <form method="post">
        <button type="submit" name="confirm" value="yes" class="btn delete-btn">Delete</button>
        <a href="view_student.php" class="btn cancel-btn">Cancel</a>
    </form>
</div>

</body>
</html>
