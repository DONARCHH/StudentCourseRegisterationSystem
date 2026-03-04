<?php
require_once "admin_auth.php";
require_once "db.php";

$id = $_GET['id'];

// Fetch student data
$res = $mysqli->query("SELECT * FROM students WHERE student_id='$id'");
$student = $res->fetch_assoc();

// Fetch distinct levels and programmes for dropdowns
$levels = [];
$res_lvl = $mysqli->query("SELECT DISTINCT level FROM students ORDER BY level");
if($res_lvl){
    while($row = $res_lvl->fetch_assoc()){
        $levels[] = $row['level'];
    }
}

$programmes = [];
$res_prog = $mysqli->query("SELECT DISTINCT programme FROM students ORDER BY programme");
if($res_prog){
    while($row = $res_prog->fetch_assoc()){
        $programmes[] = $row['programme'];
    }
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $stmt = $mysqli->prepare("UPDATE students SET first_name=?, last_name=?, phone=?, level=?, programme=?, department=? WHERE student_id=?");
    $stmt->bind_param(
        "sssssss", 
        $_POST['first_name'], 
        $_POST['last_name'], 
        $_POST['phone'], 
        $_POST['level'], 
        $_POST['programme'], 
        $_POST['department'], 
        $id
    );
    $stmt->execute();
    header("Location: view_student.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Student</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
/* Reset and body */
body {
    margin: 0;
    font-family: 'Poppins', sans-serif;
    background: #f4f6f9;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
}

/* Card container */
.edit-card {
    background: #fff;
    padding: 30px 40px;
    border-radius: 12px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.1);
    width: 100%;
    max-width: 500px;
}

/* Header */
.edit-card h2 {
    text-align: center;
    margin-bottom: 25px;
    color: #1c2b4a;
}

/* Form styling */
.edit-card form {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.edit-card label {
    font-weight: 500;
    margin-bottom: 5px;
}

.edit-card input, 
.edit-card select {
    padding: 10px 12px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 14px;
    width: 100%;
    box-sizing: border-box;
    transition: 0.3s;
}

.edit-card input:focus, 
.edit-card select:focus {
    border-color: #3949ab;
    outline: none;
}

/* Button */
.edit-card button {
    padding: 12px;
    background: #3949ab;
    color: #fff;
    font-size: 16px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    margin-top: 10px;
    transition: 0.3s;
}

.edit-card button:hover {
    background: #5c6bc0;
}
</style>
</head>
<body>

<div class="edit-card">
    <h2>Edit Student</h2>
    <form method="post">
        <div>
            <label for="first_name">First Name:</label>
            <input type="text" name="first_name" id="first_name" value="<?php echo htmlspecialchars($student['first_name']); ?>" required>
        </div>
        <div>
            <label for="last_name">Last Name:</label>
            <input type="text" name="last_name" id="last_name" value="<?php echo htmlspecialchars($student['last_name']); ?>" required>
        </div>
        <div>
            <label for="phone">Phone:</label>
            <input type="text" name="phone" id="phone" value="<?php echo htmlspecialchars($student['phone']); ?>">
        </div>
        <div>
            <label for="level">Level:</label>
            <select name="level" id="level" required>
                <?php foreach($levels as $lvl): ?>
                    <option value="<?php echo $lvl; ?>" <?php if($student['level']==$lvl) echo 'selected'; ?>><?php echo $lvl; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="programme">Programme:</label>
            <select name="programme" id="programme" required>
                <?php foreach($programmes as $prog): ?>
                    <option value="<?php echo $prog; ?>" <?php if($student['programme']==$prog) echo 'selected'; ?>><?php echo $prog; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="department">Department:</label>
            <input type="text" name="department" id="department" value="<?php echo htmlspecialchars($student['department']); ?>" required>
        </div>
        <button type="submit">Update Student</button>
    </form>
</div>

</body>
</html>
