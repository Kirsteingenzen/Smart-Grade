<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Redirect if not logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: index.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "smartgrade");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$student_id = $_SESSION['student_id'];

// Get student info from database
$sql = "SELECT student_id, full_name, gender, address, contact_number, email, password FROM students WHERE student_id = ?";
$stmt = $conn->prepare($sql);

// Check if prepare failed
if ($stmt === false) {
    die('Prepare failed for student info: ' . $conn->error . ' (Query: ' . $sql . ')');
}

$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();

// Get grades for the student, ordered by semester and subject name
$sql2 = "SELECT s.subject_name, g.grade, g.semester FROM grades g JOIN subjects s ON g.subject_id = s.id WHERE g.student_id = ? ORDER BY g.semester, s.subject_name";
$stmt2 = $conn->prepare($sql2);

// Check if prepare failed
if ($stmt2 === false) {
    die('Prepare failed for grades: ' . $conn->error . ' (Query: ' . $sql2 . ')');
}

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

// Calculate GPA for each semester
$firstSemesterGPA = 'N/A';
if (!empty($grades['1st'])) {
    $totalGrades = 0;
    $count = 0;
    foreach ($grades['1st'] as $grade) {
        $gradeValue = floatval($grade['grade']);
        if ($gradeValue > 0) { // Assuming 0 or null/empty grades shouldn't be included in average
            $totalGrades += $gradeValue;
            $count++;
        }
    }
    if ($count > 0) {
        $firstSemesterGPA = number_format($totalGrades / $count, 2);
    }
}

$secondSemesterGPA = 'N/A';
if (!empty($grades['2nd'])) {
    $totalGrades = 0;
    $count = 0;
    foreach ($grades['2nd'] as $grade) {
        $gradeValue = floatval($grade['grade']);
        if ($gradeValue > 0) { // Assuming 0 or null/empty grades shouldn't be included in average
            $totalGrades += $gradeValue;
            $count++;
        }
    }
    if ($count > 0) {
        $secondSemesterGPA = number_format($totalGrades / $count, 2);
    }
}

$stmt2->close();
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="../css/student-dashboard.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }
        .content-section {
            display: none;
        }
        .content-section.active {
            display: block !important;
        }
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 280px;
            background-color: #7F5539;
            padding: 20px;
            z-index: 1000;
        }
        .content {
            margin-left: 280px;
            padding: 20px;
            min-height: 100vh;
            width: calc(100% - 280px);
        }
        .nav-link {
            color: white;
            font-size: 18px;
            padding: 10px 15px;
            border-radius: 5px;
            transition: background-color 0.3s;
            cursor: pointer;
            background: none;
            border: none;
            width: 100%;
            text-align: left;
        }
        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }
        .nav-link.active {
            background-color: rgba(255, 255, 255, 0.2);
        }

        /* Profile Section Styles */
        .profile-header {
            background-color: #7F5539;
            padding: 2rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            position: relative;
        }
        .profile-image {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 5px solid white;
            object-fit: cover;
        }
        .profile-info {
            color: white;
            margin-left: 2rem;
        }
        .profile-info h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        .profile-info h5 {
            font-size: 1.2rem;
            opacity: 0.9;
        }
        .profile-form {
            background-color: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .form-section-title {
            color: #7F5539;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #7F5539;
        }
        .form-label {
            font-weight: 500;
            color: #495057;
        }
        .form-control {
            border: 1px solid #ced4da;
            border-radius: 5px;
            padding: 0.75rem;
        }
        .form-control:focus {
            border-color: #7F5539;
            box-shadow: 0 0 0 0.2rem rgba(127, 85, 57, 0.25);
        }
        .btn-save {
            background-color: #7F5539;
            color: white;
            padding: 0.75rem 2rem;
            border: none;
            border-radius: 5px;
            font-weight: 500;
            transition: background-color 0.3s;
        }
        .btn-save:hover {
            background-color: #6a4730;
            color: white;
        }

        /* Grades Section Styles */
        .grades-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        .grades-header {
            background-color: #7F5539;
            color: white;
            padding: 1.5rem;
            border-radius: 10px 10px 0 0;
        }
        .grades-header h2 {
            margin: 0;
            font-size: 1.5rem;
        }
        .grades-body {
            padding: 1.5rem;
        }
        .table {
            margin-bottom: 0;
        }
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        .table td {
            vertical-align: middle;
        }
    </style>
</head>
<body>
<!-- Sidebar -->
    <div class="sidebar d-flex flex-column p-3">
        <!-- Logo and Title -->
        <div class="d-flex align-items-center ps-4 pt-4">
            <img src="../assets/img/badge.png" alt="SmartGrade Logo" class="img-fluid me-2" style="width: 40px;">
            <h3 class="sg text-white mb-0">SmartGrade</h3>
        </div>   
        <div class="logo-divider"></div>
        <!-- Profile -->
        <div class="d-flex align-items-center mt-5 ms-3">
            <img src="../assets/img/profile.png" alt="Profile Icon" class="img-fluid me-2" style="width: 30px;">
            <a href="#" class="btn btn-link text-white ps-2 text-decoration-none nav-link active" style="font-size: 20px;" data-section="profile">Profile</a>
        </div>
        <!-- 1st semester -->
        <div class="d-flex align-items-center mt-4 ms-3">
            <img src="../assets/img/subjects.png" alt="Subjects Icon" class="img-fluid me-2" style="width: 30px;">
            <a href="#" class="btn btn-link text-white ps-2 text-decoration-none nav-link" style="font-size: 20px;" data-section="subjects">1st Semester</a>
        </div>
        <!-- 2nd semester -->
        <div class="d-flex align-items-center mt-4 ms-3">
            <img src="../assets/img/subjects.png" alt="SF9 Icon" class="img-fluid me-2" style="width: 30px;">
            <a href="#" class="btn btn-link text-white ps-2 text-decoration-none nav-link" style="font-size: 20px;" data-section="sf9">2nd Semester</a>
        </div>
        <!-- Logout -->
        <div class="d-flex align-items-center mt-auto pt-4 ms-3">
            <img src="../assets/img/logout.png" alt="CLogout Icon" class="img-fluid me-2" style="width: 30px;">
            <a href="logout.php" class="btn btn-link text-white ps-2 text-decoration-none " style="font-size: 20px;" data-section="logout">Log Out</a>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="content">
        <!-- Profile Content -->
        <div id="profile" class="content-section active p-4">
            <!-- Profile Header -->
            <div class="profile-header d-flex align-items-center">
                <img src="../assets/img/dog.jpg" alt="Profile" class="profile-image">
                <div class="profile-info">
                    <h1><?php echo htmlspecialchars($student['full_name']); ?></h1>
                    <h5>Student ID: <?php echo htmlspecialchars($student['student_id']); ?></h5>
                </div>
            </div>

            <!-- Profile Form -->
            <div class="profile-form">
                <h5 class="form-section-title">Personal Information</h5>
                <form class="row g-4">
                    <div class="col-md-6">
                        <label for="fullName" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="fullName" value="<?php echo htmlspecialchars($student['full_name']); ?>" disabled>
                    </div>
                    <div class="col-md-6">
                        <label for="studentidnumber" class="form-label">Student ID Number</label>
                        <input type="text" class="form-control" id="studentidnumber" value="<?php echo htmlspecialchars($student['student_id']); ?>" disabled>
                    </div>
                    <div class="col-md-6">
                        <label for="sex" class="form-label">Sex</label>
                        <input type="text" class="form-control" id="sex" value="<?php echo htmlspecialchars($student['gender']); ?>" disabled>
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" value="<?php echo htmlspecialchars($student['email']); ?>" disabled>
                    </div>
                    <div class="col-md-12">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" class="form-control" id="address" value="<?php echo htmlspecialchars($student['address']); ?>" disabled>
                    </div>
                    <div class="col-md-6">
                        <label for="contact" class="form-label">Contact Number</label>
                        <input type="text" class="form-control" id="contact" value="<?php echo htmlspecialchars($student['contact_number']); ?>" disabled>
                    </div>
                    <div class="col-md-6">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" value="<?php echo htmlspecialchars($student['password']); ?>" disabled>
                    </div>
                </form>

                <!-- Buttons -->
                <div class="mt-4 text-end">
                    <button type="button" class="btn btn-secondary me-2" id="editBtn">Edit</button>
                    <button type="button" class="btn btn-save" id="saveBtn">Save Changes</button>
                </div>
            </div>
        </div>

        <!-- Subjects Content (1st Semester) -->
        <div id="subjects" class="content-section p-4">
            <div class="grades-card">
                <div class="grades-header">
                    <h2>1st Semester Grades</h2>
                </div>
                <div class="grades-body">
                    <?php if (!empty($grades['1st'])): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Subject</th>
                                        <th>Grade</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($grades['1st'] as $grade): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($grade['subject_name']); ?></td>
                                            <td><?php echo htmlspecialchars($grade['grade']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No grades found for 1st Semester.</p>
                    <?php endif; ?>
                    <!-- GPA Row for 1st Semester -->
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <strong>Semester GPA:</strong>
                        </div>
                        <div class="col-md-6">
                            <?php echo htmlspecialchars($firstSemesterGPA); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2nd Semester Content -->
        <div id="sf9" class="content-section p-4">
            <div class="grades-card">
                <div class="grades-header">
                    <h2>2nd Semester Grades</h2>
                </div>
                <div class="grades-body">
                    <?php if (!empty($grades['2nd'])): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Subject</th>
                                        <th>Grade</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($grades['2nd'] as $grade): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($grade['subject_name']); ?></td>
                                            <td><?php echo htmlspecialchars($grade['grade']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No grades found for 2nd Semester.</p>
                    <?php endif; ?>
                    <!-- GPA Row for 2nd Semester -->
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <strong>Semester GPA:</strong>
                        </div>
                        <div class="col-md-6">
                            <?php echo htmlspecialchars($secondSemesterGPA); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- SimpleLightbox plugin JS-->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/SimpleLightbox/2.1.0/simpleLightbox.min.js"></script>
        <!-- Core theme JS-->
        <script src="../js/student-dashboard.js"></script>
        <!-- * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *-->
        <!-- * *                               SB Forms JS                               * *-->
        <!-- * * Activate your form at https://startbootstrap.com/solution/contact-forms * *-->
        <!-- * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *-->
        <script src="https://cdn.startbootstrap.com/sb-forms-latest.js"></script>

    <script>
        // Function to switch between sections
        function switchSection(sectionId) {
            // Hide all sections
            document.querySelectorAll('.content-section').forEach(section => {
                section.style.display = 'none';
                section.classList.remove('active');
            });
            
            // Remove active class from all nav links
            document.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('active');
            });
            
            // Show selected section
            const selectedSection = document.getElementById(sectionId);
            if (selectedSection) {
                selectedSection.style.display = 'block';
                selectedSection.classList.add('active');
            }
            
            // Activate selected nav link
            const selectedLink = document.querySelector(`.nav-link[data-section="${sectionId}"]`);
            if (selectedLink) {
                selectedLink.classList.add('active');
            }
        }

        // Initialize the page
        document.addEventListener('DOMContentLoaded', function() {
            // Show profile section by default
            switchSection('profile');

            // Add click event listeners to navigation links
            document.querySelectorAll('.nav-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const sectionId = this.getAttribute('data-section');
                    if (sectionId) {
                        switchSection(sectionId);
                    }
                });
            });
        });
    </script>
</body>
</html>