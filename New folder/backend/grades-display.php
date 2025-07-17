<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: index.php");
    exit();
}

// Get the selected semester from URL parameter, default to 1st
$selected_semester = isset($_GET['semester']) ? $_GET['semester'] : '1st';

// Database connection
$conn = new mysqli("localhost", "root", "", "smartgrade");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$student_id = $_SESSION['student_id'];

// Get student info
$stmt = $conn->prepare("SELECT full_name, student_id FROM students WHERE student_id = ?");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();

// Get grades
$stmt2 = $conn->prepare("SELECT subject, grade, semester FROM grades WHERE student_id = ? ORDER BY semester, subject");
$stmt2->bind_param("s", $student_id);
$stmt2->execute();
$result2 = $stmt2->get_result();

// Organize grades by semester
$grades = ['1st' => [], '2nd' => []];
while ($row = $result2->fetch_assoc()) {
    if ($row['semester'] === '1st' || $row['semester'] === '2nd') {
        $grades[$row['semester']][] = $row;
    }
}

$stmt2->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Grades</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 280px;
            background-color: #7F5539;
            padding: 20px;
        }
        .content {
            margin-left: 280px;
            padding: 20px;
        }
        .logo-divider {
            height: 2px;
            background-color: rgba(255, 255, 255, 0.2);
            margin: 20px 0;
        }
        .nav-link {
            color: white;
            font-size: 18px;
            padding: 10px 15px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }
        .nav-link.active {
            background-color: rgba(255, 255, 255, 0.2);
        }
        .grade-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .grade-header {
            background-color: #7F5539;
            color: white;
            padding: 15px;
            border-radius: 10px 10px 0 0;
        }
        .table {
            margin-bottom: 0;
        }
        .table th {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="d-flex align-items-center">
            <img src="../assets/img/badge.png" alt="SmartGrade Logo" class="img-fluid me-2" style="width: 40px;">
            <h3 class="text-white mb-0">SmartGrade</h3>
        </div>
        <div class="logo-divider"></div>
        
        <div class="mt-4">
            <a href="student-dashboard.php" class="nav-link d-flex align-items-center">
                <img src="../assets/img/profile.png" alt="Profile" class="me-2" style="width: 24px;">
                Profile
            </a>
            <a href="grades-display.php?semester=1st" class="nav-link d-flex align-items-center mt-2 <?php echo $selected_semester === '1st' ? 'active' : ''; ?>">
                <img src="../assets/img/subjects.png" alt="1st Semester" class="me-2" style="width: 24px;">
                1st Semester
            </a>
            <a href="grades-display.php?semester=2nd" class="nav-link d-flex align-items-center mt-2 <?php echo $selected_semester === '2nd' ? 'active' : ''; ?>">
                <img src="../assets/img/subjects.png" alt="2nd Semester" class="me-2" style="width: 24px;">
                2nd Semester
            </a>
            <a href="logout.php" class="nav-link d-flex align-items-center mt-2">
                <img src="../assets/img/logout.png" alt="Logout" class="me-2" style="width: 24px;">
                Logout
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col">
                    <h2 class="mb-0"><?php echo htmlspecialchars($student['full_name']); ?></h2>
                    <p class="text-muted">Student ID: <?php echo htmlspecialchars($student['student_id']); ?></p>
                </div>
            </div>

            <!-- Grades Card -->
            <div class="grade-card">
                <div class="grade-header">
                    <h4 class="mb-0"><?php echo $selected_semester; ?> Semester Grades</h4>
                </div>
                <div class="p-4">
                    <?php if (!empty($grades[$selected_semester])): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Subject</th>
                                        <th>Grade</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($grades[$selected_semester] as $grade): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($grade['subject']); ?></td>
                                        <td><?php echo htmlspecialchars($grade['grade']); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info mb-0">
                            No grades found for <?php echo $selected_semester; ?> Semester.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 