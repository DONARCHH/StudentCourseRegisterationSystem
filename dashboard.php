<?php
require_once "admin_auth.php";
require_once "db.php";

// ========== TOTALS ==========
$total_students = $mysqli->query("SELECT COUNT(*) as total FROM students")->fetch_assoc()['total'];
$total_programmes = $mysqli->query("SELECT COUNT(DISTINCT programme) as total FROM students")->fetch_assoc()['total'];
$total_courses = $mysqli->query("SELECT COUNT(*) as total FROM courses")->fetch_assoc()['total'];

// ========== RECENT STUDENTS ==========
$recent_students = [];
$res = $mysqli->query("SELECT first_name, last_name, programme, level, phone FROM students ORDER BY student_id ASC LIMIT 6");
while($row = $res->fetch_assoc()) { 
    $recent_students[] = $row; 
}

// ========== ENROLLMENT TREND (CHART) ==========
$enrollment_trend = [];
$months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
$result = $mysqli->query("SELECT MONTH(date_registered) as month, COUNT(*) as total FROM registration GROUP BY month");
while($row = $result->fetch_assoc()){
    $enrollment_trend[$row['month']] = $row['total'];
}
$enrollment_data = [];
for($i=1;$i<=12;$i++){ $enrollment_data[] = isset($enrollment_trend[$i]) ? $enrollment_trend[$i] : 0; }

// ========== COURSES DISTRIBUTION ==========
$labels = [];
$courses_dist = [];
$res2 = $mysqli->query("SELECT programme, COUNT(*) as total FROM students GROUP BY programme");
while($row = $res2->fetch_assoc()){
    $labels[] = $row['programme'];
    $courses_dist[] = $row['total'];
    
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="dashboardd.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<!-- ========== SIDEBAR ========== -->
<div class="sidebar">
    <div class="sidebar-header">
        <h2>Admin Panel</h2>
    </div>
    <div class="sidebar-nav">
        <a href="dashboard.php" class="active">Dashboard</a>
        <a href="rg.php">Registered Courses</a>
        <a href="view_student.php">View Students</a>
        <a href="courses.php">Courses</a>
        <a href="logout.php" class="logout">Logout</a>
    </div>
</div>

<!-- ========== MAIN CONTENT ========== -->
<div class="main">
    <!-- TOPBAR -->
    <div class="topbar">
        <h2>Welcome, <?php echo $_SESSION['admin_name']; ?></h2>
        <strong>Admin</strong>
    </div>

    <!-- KPI CARDS -->
    <div class="cards">
        <div class="card">
            <h3>Total Students</h3>
            <p><?php echo $total_students; ?></p>
        </div>
        <div class="card">
            <h3>Total Programmes</h3>
            <p><?php echo $total_programmes; ?></p>
        </div>
        <div class="card">
            <h3>Total Courses</h3>
            <p><?php echo $total_courses; ?></p>
        </div>
    </div>

    <!-- CHARTS -->
    <div class="charts">
        <div class="chart-container">
            <h3>Enrollment Trend</h3>
            <canvas id="enrollmentChart"></canvas>
        </div>
        <div class="chart-container">
            <h3>Courses Distribution</h3>
            <canvas id="coursesChart"></canvas>
        </div>
    </div>

    <!-- RECENT STUDENTS -->
    <section class="recent-students-section">
        <h2>Recent Students</h2>
        <div class="students-grid">
            <?php
            if(!empty($recent_students)){
                foreach($recent_students as $student){
                    $full_name = $student['first_name'] . ' ' . $student['last_name'];
                    echo "<div class='student-card'>
                            <div class='student-avatar'>
                                <span>".strtoupper($student['first_name'][0].$student['last_name'][0])."</span>
                            </div>
                            <div class='student-info'>
                                <h3>{$full_name}</h3>
                                <p><strong>Programme:</strong> {$student['programme']}</p>
                                <p><strong>Level:</strong> {$student['level']}</p>
                                <p><strong>Phone:</strong> {$student['phone']}</p>
                            </div>
                        </div>";
                }
            } else {
                echo "<p class='no-students'>No students found</p>";
            }
            ?>
        </div>
    </section>
</div>

<!-- ========== CHART JS ========== -->
<script>
const enrollmentCtx = document.getElementById('enrollmentChart').getContext('2d');
const enrollmentChart = new Chart(enrollmentCtx, {
    type:'line',
    data:{
        labels: <?php echo json_encode($months); ?>,
        datasets:[{
            label:'Students Enrolled',
            data: <?php echo json_encode($enrollment_data); ?>,
            backgroundColor:'rgba(28,43,74,0.2)',
            borderColor:'#1c2b4a',
            borderWidth:2,
            fill:true,
            tension:0.3
        }]
    },
    options:{responsive:true, plugins:{legend:{display:false}}}
});

const coursesCtx = document.getElementById('coursesChart').getContext('2d');
const coursesChart = new Chart(coursesCtx,{
    type:'doughnut',
    data:{
        labels: <?php echo json_encode($labels); ?>,
        datasets:[{
            data: <?php echo json_encode($courses_dist); ?>,
            backgroundColor:['#1c2b4a','#4cd137','#ffa500','#ff6b6b','#00a8ff','#8e44ad','#3498db'],
            borderWidth:1
        }]
    },
    options:{responsive:true, plugins:{legend:{position:'bottom'}}}
});
</script>
<script>
// Animate recent students cards
document.querySelectorAll('.student-card').forEach((card, index) => {
    setTimeout(() => {
        card.classList.add('show');
    }, 100 * index); // stagger animation by 100ms
});

// Animate chart containers
document.querySelectorAll('.chart-container').forEach((chart, index) => {
    setTimeout(() => {
        chart.classList.add('show');
    }, 300 * index); // stagger animation
});
</script>

</body>
</html>
