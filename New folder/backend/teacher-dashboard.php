<?php
// Get teacher ID from session
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}

$teacherUsername = $_SESSION['username'];

// Database connection parameters
$host = 'localhost';
$dbname = 'smartgrade';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepare and execute query to get teacher data using username
    $stmt = $pdo->prepare("SELECT id, username, full_name, sex, address, contact_number, email FROM teachers WHERE username = ?");
    $stmt->execute([$teacherUsername]);
    $teacher = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$teacher) {
        // Handle no teacher found
        $teacher = [
            'id' => '',
            'username' => '',
            'full_name' => '',
            'sex' => '',
            'address' => '',
            'contact_number' => '',
            'email' => '',
        ];
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teacher Dashboard</title>
    <link rel="stylesheet" href="../css/teacher-dashboard.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
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
            font-size: 16px;
            padding: 8px 12px;
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
            font-size: 14px;
        }
        .table th {
            background-color: #f8f9fa;
            font-size: 14px;
            padding: 8px;
        }
        .table td {
            padding: 8px;
            vertical-align: middle;
        }
        .btn-check:checked + .btn-outline-primary {
            background-color: #7F5539;
            border-color: #7F5539;
            color: white;
        }
        .btn-outline-primary {
            color: #7F5539;
            border-color: #7F5539;
        }
        .btn-outline-primary:hover {
            background-color: #7F5539;
            border-color: #7F5539;
            color: white;
        }
        .profile-overlay {
            background-color: #7F5539 !important;
        }
        .btn-primary {
            background-color: #7F5539;
            border-color: #7F5539;
        }
        .btn-primary:hover {
            background-color: #6a4730;
            border-color: #6a4730;
        }
        .card-header {
            background-color: #7F5539 !important;
        }

        /* New Profile Section Styles */
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
            font-size: 14px;
            padding: 4px 8px;
            height: auto;
            border: 1px solid #ced4da;
            border-radius: 5px;
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
        .btn-sm {
            font-size: 10px;
            padding: 4px 8px;
        }

        /* CSS for highlighting GPA >= 90 */
        .highlight-gpa {
            color: green;
            font-weight: bold;
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
            <button type="button" class="nav-link active" data-section="profile">Profile</button>
        </div>
        <!-- Subjects -->
        <div class="d-flex align-items-center mt-3 ms-3">
            <img src="../assets/img/subjects.png" alt="Subjects Icon" class="img-fluid me-2" style="width: 30px;">
            <button type="button" class="nav-link" data-section="subjects">Subjects</button>
        </div>
        <!-- Student Management -->
        <div class="d-flex align-items-center mt-3 ms-3">
            <img src="../assets/img/subjects.png" alt="Student Management Icon" class="img-fluid me-2" style="width: 30px;">
            <button type="button" class="nav-link" data-section="student-management">Student Management</button>
        </div>
        <!-- Logout -->
        <div class="d-flex align-items-center mt-auto pt-4 ms-3">
            <img src="../assets/img/logout.png" alt="Logout Icon" class="img-fluid me-2" style="width: 30px;">
            <a href="logout.php" class="btn btn-link text-white ps-2 text-decoration-none" style="font-size: 20px;">Log Out</a>
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
                    <h1><?php echo htmlspecialchars($teacher['full_name']); ?></h1>
                    <h5>username: <?php echo htmlspecialchars($teacher['username']); ?></h5>
                </div>
            </div>

            <!-- Profile Form -->
            <div class="profile-form">
                <h5 class="form-section-title">Personal Information</h5>
                <form method="post" id="teacherProfileForm">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label for="fullName" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="fullName" name="fullName" 
                                   value="<?php echo htmlspecialchars($teacher['full_name']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="sex" class="form-label">Sex</label>
                            <input type="text" class="form-control" id="sex" name="sex" 
                                   value="<?php echo htmlspecialchars($teacher['sex']); ?>" required>
                        </div>
                        <div class="col-md-12">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" class="form-control" id="address" name="address" 
                                   value="<?php echo htmlspecialchars($teacher['address']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="contact" class="form-label">Contact Number</label>
                            <input type="text" class="form-control" id="contact" name="contact" 
                                   value="<?php echo htmlspecialchars($teacher['contact_number']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($teacher['email']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password">
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-save">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Subjects Content -->
        <div id="subjects" class="content-section p-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="mb-0">Grade Management</h4>
                </div>
                <div class="card-body">
                    <!-- Semester Selection -->
                    <div class="mb-4">
                        <div class="btn-group" role="group" aria-label="Semester selection">
                            <?php
                            // Get the selected semester from GET parameter or default to 1st
                            $selectedSemester = isset($_GET['semester']) ? $_GET['semester'] : '1st';
                            ?>
                            <input type="radio" class="btn-check" name="semester" id="semester1" value="1st" <?php echo ($selectedSemester === '1st') ? 'checked' : ''; ?>>
                            <label class="btn btn-outline-primary" for="semester1">1st Semester</label>
                            <input type="radio" class="btn-check" name="semester" id="semester2" value="2nd" <?php echo ($selectedSemester === '2nd') ? 'checked' : ''; ?>>
                            <label class="btn btn-outline-primary" for="semester2">2nd Semester</label>
                        </div>
                    </div>

                    <!-- Loading spinner -->
                    <div id="loadingSpinner" class="text-center" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        Loading data...
                    </div>

                    <!-- Content will be loaded here by AJAX -->
                    <div id="subjectsContent">
                        <?php
                        // Initial load of data for the default semester (1st) or selected via GET
                        // This block is primarily for the initial render. AJAX will take over for subsequent changes.

                        try {
                            // Fetch all subjects
                            $stmtSubjects = $pdo->query("SELECT id, subject_name FROM subjects ORDER BY subject_name");
                            $subjects = $stmtSubjects->fetchAll(PDO::FETCH_ASSOC);

                            // Fetch all students with their grades for the selected semester
                            $stmtStudentsGrades = $pdo->prepare("
                                SELECT s.student_id, s.full_name, s.gender, g.subject_id, g.grade
                                FROM students s
                                LEFT JOIN grades g ON s.student_id = g.student_id AND g.semester = ?
                                ORDER BY s.gender, s.full_name
                            ");
                            $stmtStudentsGrades->execute([$selectedSemester]);
                            $studentsGrades = $stmtStudentsGrades->fetchAll(PDO::FETCH_ASSOC);

                            // Process the data to group grades by student
                            $processedStudents = [];
                            foreach ($studentsGrades as $row) {
                                $studentId = $row['student_id'];
                                if (!isset($processedStudents[$studentId])) {
                                    $processedStudents[$studentId] = [
                                        'student_id' => $row['student_id'],
                                        'full_name' => $row['full_name'],
                                        'gender' => $row['gender'],
                                        'grades' => []
                                    ];
                                }
                                if ($row['subject_id'] !== null) {
                                    $processedStudents[$studentId]['grades'][$row['subject_id']] = $row['grade'];
                                }
                            }

                            // Separate processed students by gender
                            $maleStudents = array_filter($processedStudents, function($student) {
                                return $student['gender'] === 'Male';
                            });
                            $femaleStudents = array_filter($processedStudents, function($student) {
                                return $student['gender'] === 'Female';
                            });

                        } catch (PDOException $e) {
                            echo '<div class="alert alert-danger">Database error: ' . $e->getMessage() . '</div>';
                            $subjects = [];
                            $maleStudents = [];
                            $femaleStudents = [];
                        }
                        ?>

                        <!-- Male Students Table -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Male Students</h5>
                            </div>
                            <div class="card-body">
                                <?php if (empty($maleStudents)): ?>
                                    <p>No male students found.</p>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Student ID</th>
                                                    <th>Name</th>
                                                    <?php foreach ($subjects as $subject): ?>
                                                        <th><?php echo htmlspecialchars($subject['subject_name']); ?></th>
                                                    <?php endforeach; ?>
                                                    <th>GPA</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($maleStudents as $student): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                                                        <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                                                        <?php foreach ($subjects as $subject): ?>
                                                            <td>
                                                                <input type="number" class="form-control grade-input" 
                                                                       data-student-id="<?php echo htmlspecialchars($student['student_id']); ?>"
                                                                       data-subject-id="<?php echo $subject['id']; ?>"
                                                                       value="<?php echo htmlspecialchars($student['grades'][$subject['id']] ?? ''); ?>"
                                                                       step="0.01" min="0" max="100">
                                                            </td>
                                                        <?php endforeach; ?>
                                                        <td>
                                                            <?php
                                                            // Calculate GPA (average grade) for the student
                                                            $totalGrades = 0;
                                                            $gradedSubjectsCount = 0;
                                                            foreach ($student['grades'] as $grade) {
                                                                $totalGrades += floatval($grade);
                                                                $gradedSubjectsCount++;
                                                            }
                                                            $gpa = $gradedSubjectsCount > 0 ? ($totalGrades / $gradedSubjectsCount) : 'N/A';
                                                            ?>
                                                            <span class="<?php echo $gpa >= 90 ? 'highlight-gpa' : ''; ?>">
                                                                <?php echo htmlspecialchars($gpa); ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <div class="dropdown">
                                                                <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton_<?php echo htmlspecialchars($student['student_id']); ?>" data-bs-toggle="dropdown" aria-expanded="false">
                                                                    Actions
                                                                </button>
                                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton_<?php echo htmlspecialchars($student['student_id']); ?>">
                                                                    <li><a class="dropdown-item save-grades" href="#" data-student-id="<?php echo htmlspecialchars($student['student_id']); ?>">Save</a></li>
                                                                    <li><a class="dropdown-item generate-report-card" href="#" data-student-id="<?php echo htmlspecialchars($student['student_id']); ?>" data-student-name="<?php echo htmlspecialchars($student['full_name']); ?>">Report Card</a></li>
                                                                    <li><a class="dropdown-item generate-sf10" href="#" data-student-id="<?php echo htmlspecialchars($student['student_id']); ?>" data-student-name="<?php echo htmlspecialchars($student['full_name']); ?>">SF10</a></li>
                                                                    <li><a class="dropdown-item generate-certificate" href="#" data-student-id="<?php echo htmlspecialchars($student['student_id']); ?>" data-student-name="<?php echo htmlspecialchars($student['full_name']); ?>">Certificate</a></li>
                                                                </ul>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Female Students Table -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Female Students</h5>
                            </div>
                            <div class="card-body">
                                 <?php if (empty($femaleStudents)): ?>
                                    <p>No female students found.</p>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Student ID</th>
                                                    <th>Name</th>
                                                    <?php foreach ($subjects as $subject): ?>
                                                        <th><?php echo htmlspecialchars($subject['subject_name']); ?></th>
                                                    <?php endforeach; ?>
                                                    <th>GPA</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($femaleStudents as $student): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                                                        <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                                                        <?php foreach ($subjects as $subject): ?>
                                                            <td>
                                                                <input type="number" class="form-control grade-input" 
                                                                       data-student-id="<?php echo htmlspecialchars($student['student_id']); ?>"
                                                                       data-subject-id="<?php echo $subject['id']; ?>"
                                                                       value="<?php echo htmlspecialchars($student['grades'][$subject['id']] ?? ''); ?>"
                                                                       step="0.01" min="0" max="100">
                                                            </td>
                                                        <?php endforeach; ?>
                                                        <td>
                                                            <?php
                                                            // Calculate GPA (average grade) for the student
                                                            $totalGrades = 0;
                                                            $gradedSubjectsCount = 0;
                                                            foreach ($student['grades'] as $grade) {
                                                                $totalGrades += floatval($grade);
                                                                $gradedSubjectsCount++;
                                                            }
                                                            $gpa = $gradedSubjectsCount > 0 ? ($totalGrades / $gradedSubjectsCount) : 'N/A';
                                                            ?>
                                                            <span class="<?php echo $gpa >= 90 ? 'highlight-gpa' : ''; ?>">
                                                                <?php echo htmlspecialchars($gpa); ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <div class="dropdown">
                                                                <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton_<?php echo htmlspecialchars($student['student_id']); ?>" data-bs-toggle="dropdown" aria-expanded="false">
                                                                    Actions
                                                                </button>
                                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton_<?php echo htmlspecialchars($student['student_id']); ?>">
                                                                    <li><a class="dropdown-item save-grades" href="#" data-student-id="<?php echo htmlspecialchars($student['student_id']); ?>">Save</a></li>
                                                                    <li><a class="dropdown-item generate-report-card" href="#" data-student-id="<?php echo htmlspecialchars($student['student_id']); ?>" data-student-name="<?php echo htmlspecialchars($student['full_name']); ?>">Report Card</a></li>
                                                                    <li><a class="dropdown-item generate-sf10" href="#" data-student-id="<?php echo htmlspecialchars($student['student_id']); ?>" data-student-name="<?php echo htmlspecialchars($student['full_name']); ?>">SF10</a></li>
                                                                    <li><a class="dropdown-item generate-certificate" href="#" data-student-id="<?php echo htmlspecialchars($student['student_id']); ?>" data-student-name="<?php echo htmlspecialchars($student['full_name']); ?>">Certificate</a></li>
                                                                </ul>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Student Management Content -->
        <div id="student-management" class="content-section p-4">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Student Management</h4>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                        <i class="fas fa-plus"></i> Add New Student
                    </button>
                </div>
                <div class="card-body">
                    <!-- Search Bar -->
                    <div class="mb-4">
                        <input type="text" id="studentSearch" class="form-control" placeholder="Search students...">
                    </div>

                    <!-- Students Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Student ID</th>
                                    <th>Full Name</th>
                                    <th>Gender</th>
                                    <th>Address</th>
                                    <th>Contact Number</th>
                                    <th>Email</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="studentsTableBody">
                                <!-- Students will be loaded here dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add/Edit Student Modal -->
        <div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addStudentModalLabel">Add New Student</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="studentForm">
                            <input type="hidden" name="action" value="add">
                            <input type="hidden" name="student_id" id="editStudentId">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="studentIdInput" class="form-label">Student ID</label>
                                    <input type="text" class="form-control" id="studentIdInput" name="student_id" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="fullName" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="fullName" name="full_name" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="gender" class="form-label">Gender</label>
                                    <select class="form-select" id="gender" name="gender" required>
                                        <option value="">Select Gender</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="contactNumber" class="form-label">Contact Number</label>
                                    <input type="text" class="form-control" id="contactNumber" name="contact_number" required>
                                </div>
                                <div class="col-md-12">
                                    <label for="address" class="form-label">Address</label>
                                    <input type="text" class="form-control" id="address" name="address" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="saveStudentBtn">Save Student</button>
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
    <script src="../js/teacher-dashboard.js"></script>
    <!-- SB Forms JS-->
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

            // Store the current section in sessionStorage
            sessionStorage.setItem('currentSection', sectionId);
        }

        // Function to fetch and display semester data
        function fetchAndDisplaySemesterData(semester) {
            const subjectsContentDiv = document.getElementById('subjectsContent');
            const loadingSpinner = document.getElementById('loadingSpinner');

            // Show loading spinner and hide content
            if (subjectsContentDiv) subjectsContentDiv.style.display = 'none';
            if (loadingSpinner) loadingSpinner.style.display = 'block';

            fetch('/New folder/backend/fetch_semester_data.php?semester=' + encodeURIComponent(semester))
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => {
                            throw new Error(`HTTP error! status: ${response.status}, Body: ${text}`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        renderSubjectsContent(data.data.subjects, data.data.maleStudents, data.data.femaleStudents);
                    } else {
                        console.error('Error fetching semester data:', data.message);
                        subjectsContentDiv.innerHTML = '<div class="alert alert-danger">Error loading data: ' + data.message + '</div>';
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    subjectsContentDiv.innerHTML = '<div class="alert alert-danger">Error loading data. Please try again.</div>';
                })
                .finally(() => {
                    // Hide loading spinner and show content
                    if (loadingSpinner) loadingSpinner.style.display = 'none';
                    if (subjectsContentDiv) subjectsContentDiv.style.display = 'block';
                });
        }

        // Function to render the subjects content dynamically
        function renderSubjectsContent(subjects, maleStudents, femaleStudents) {
            const subjectsContentDiv = document.getElementById('subjectsContent');
            if (!subjectsContentDiv) return;

            let html = '';

            // Helper function to render a student table
            function renderStudentTable(students, gender) {
                if (students.length === 0) {
                    return `<p>No ${gender.toLowerCase()} students found.</p>`;
                }

                let tableHtml = `
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Student ID</th>
                                    <th>Name</th>
                                    ${subjects.map(subject => `<th>${escapeHTML(subject.subject_name)}</th>`).join('')}>
                                    <th>GPA</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                `;

                students.forEach(student => {
                    // Calculate GPA (average grade) for the student
                    let totalGrades = 0;
                    let gradedSubjectsCount = 0;
                    subjects.forEach(subject => {
                        const grade = parseFloat(student.grades[subject.id]);
                        if (!isNaN(grade)) {
                            totalGrades += grade;
                            gradedSubjectsCount++;
                        }
                    });
                    const gpa = gradedSubjectsCount > 0 ? (totalGrades / gradedSubjectsCount).toFixed(2) : 'N/A';

                    tableHtml += `
                        <tr>
                            <td>${escapeHTML(student.student_id)}</td>
                            <td>${escapeHTML(student.full_name)}</td>
                            ${subjects.map(subject => `
                                <td>
                                    <input type="number" class="form-control grade-input" 
                                           data-student-id="${escapeHTML(student.student_id)}"
                                           data-subject-id="${escapeHTML(subject.id)}"
                                           value="${escapeHTML(student.grades[subject.id] ?? '')}"
                                           step="0.01" min="0" max="100">
                                </td>
                            `).join('')}>
                            <td class="${gpa >= 90 ? 'highlight-gpa' : ''}">${gpa}</td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton_${escapeHTML(student.student_id)}" data-bs-toggle="dropdown" aria-expanded="false">
                                        Actions
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton_${escapeHTML(student.student_id)}">
                                        <li><a class="dropdown-item save-grades" href="#" data-student-id="${escapeHTML(student.student_id)}">Save</a></li>
                                        <li><a class="dropdown-item generate-report-card" href="#" data-student-id="${escapeHTML(student.student_id)}" data-student-name="${escapeHTML(student.full_name)}">Report Card</a></li>
                                        <li><a class="dropdown-item generate-sf10" href="#" data-student-id="${escapeHTML(student.student_id)}" data-student-name="${escapeHTML(student.full_name)}">SF10</a></li>
                                        <li><a class="dropdown-item generate-certificate" href="#" data-student-id="${escapeHTML(student.student_id)}" data-student-name="${escapeHTML(student.full_name)}">Certificate</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    `;
                });

                tableHtml += `
                            </tbody>
                        </table>
                    </div>
                `;

                return tableHtml;
            }

            // Render Male Students Section
            html += `
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Male Students</h5>
                    </div>
                    <div class="card-body">
                        ${renderStudentTable(maleStudents, 'Male')}
                    </div>
                </div>
            `;

            // Render Female Students Section
            html += `
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Female Students</h5>
                    </div>
                    <div class="card-body">
                        ${renderStudentTable(femaleStudents, 'Female')}
                    </div>
                </div>
            `;

            subjectsContentDiv.innerHTML = html;

            // Re-attach save button event listeners after rendering
            attachSubjectsButtonListeners();
        }

        // Helper function to escape HTML for safety
        function escapeHTML(str) {
            if (str === null || str === undefined) return '';
            const div = document.createElement('div');
            div.appendChild(document.createTextNode(str));
            return div.innerHTML;
        }

        // Function to attach event listeners to buttons in Subjects section
        function attachSubjectsButtonListeners() {
             // Generate Report Card button listeners
            document.querySelectorAll('.generate-report-card').forEach(button => {
                button.addEventListener('click', function() {
                    const studentId = this.getAttribute('data-student-id');
                    const studentName = this.getAttribute('data-student-name');
                    // Assuming sf9_merged.html is in the 'school card' directory one level up from 'backend'
                    window.open(`../../school card/sf9_merged.html?student=${encodeURIComponent(JSON.stringify({ id: studentId, name: studentName }))}`, '_blank');
                });
            });

            // Generate Certificate button listeners
            document.querySelectorAll('.generate-certificate').forEach(button => {
                button.addEventListener('click', function() {
                    const studentId = this.getAttribute('data-student-id');
                    const studentName = this.getAttribute('data-student-name');
                    // Assuming certificate/index.html is two levels up from 'backend'
                    window.open(`../../certificate/index.html?student=${encodeURIComponent(studentName)}`, '_blank');
                });
            });

             // Save grades button listeners
            document.querySelectorAll('.save-grades').forEach(button => {
                console.log('Attaching save listener to button:', button);
                button.addEventListener('click', function() {
                    console.log('Save button clicked!');
                    const studentId = this.getAttribute('data-student-id');
                    const row = this.closest('tr');
                    const gradeInputs = row.querySelectorAll('.grade-input');
                    const semester = document.querySelector('input[name="semester"]:checked').value;

                    const gradesToSave = Array.from(gradeInputs).map(input => ({
                        student_id: studentId,
                        subject_id: input.getAttribute('data-subject-id'),
                        grade: input.value,
                        semester: semester
                    })).filter(grade => grade.grade !== ''); // Only send grades that are not empty

                    console.log('Student ID:', studentId);
                    console.log('Semester:', semester);
                    console.log('Grades to save:', gradesToSave);

                    if (gradesToSave.length === 0) {
                        console.log('No grades entered to save for this student.');
                        // Optionally show a message to the user
                        return;
                    }

                    // Disable the save button while processing
                    this.disabled = true;
                    this.textContent = 'Saving...';

                    // Send grades to server
                    fetch('grade_operations.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            action: 'update',
                            grades: gradesToSave
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            // Attempt to read the response body for more detailed error from PHP
                            return response.text().then(text => {
                                throw new Error(`HTTP error! status: ${response.status}, Body: ${text}`);
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Show success message
                            const successAlert = document.createElement('div');
                            successAlert.className = 'alert alert-success alert-dismissible fade show mt-2';
                            successAlert.innerHTML = `
                                ${escapeHTML(data.message)}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            `;
                            // Find the nearest ancestor card-body or similar container to insert the alert
                            const container = row.closest('.card-body') || row.closest('.table-responsive') || row.closest('table');
                            if(container) {
                                container.insertAdjacentElement('beforebegin', successAlert);
                            } else {
                                row.insertAdjacentElement('beforebegin', successAlert);
                            }

                            // Remove alert after 3 seconds
                            setTimeout(() => {
                                successAlert.remove();
                            }, 3000);

                            // *** Add this line to refetch and display data after successful save ***
                            fetchAndDisplaySemesterData(semester);

                        } else {
                            throw new Error(data.message || 'Unknown error occurred');
                        }
                    })
                    .catch(error => {
                        console.error('Error saving grades:', error);
                        // Show error message
                        const errorAlert = document.createElement('div');
                        errorAlert.className = 'alert alert-danger alert-dismissible fade show mt-2';
                        errorAlert.innerHTML = `
                            Error: ${escapeHTML(error.message)}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        `;
                        // Find the nearest ancestor card-body or similar container to insert the alert
                        const container = row.closest('.card-body') || row.closest('.table-responsive') || row.closest('table');
                        if(container) {
                            container.insertAdjacentElement('beforebegin', errorAlert);
                        } else {
                            row.insertAdjacentElement('beforebegin', errorAlert);
                        }

                        // Remove alert after 3 seconds
                        setTimeout(() => {
                            errorAlert.remove();
                        }, 3000);
                    })
                    .finally(() => {
                        // Re-enable the save button
                        this.disabled = false;
                        this.textContent = 'Save';
                    });
                });
            });
        }

        // Student Management Functions
        function loadStudents() {
            fetch('student_operations.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ action: 'get' })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderStudentsTable(data.data);
                } else {
                    console.error('Error loading students:', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        function renderStudentsTable(students) {
            const tbody = document.getElementById('studentsTableBody');
            if (!tbody) return;

            tbody.innerHTML = students.map(student => `
                <tr>
                    <td>${escapeHTML(student.student_id)}</td>
                    <td>${escapeHTML(student.full_name)}</td>
                    <td>${escapeHTML(student.gender)}</td>
                    <td>${escapeHTML(student.address)}</td>
                    <td>${escapeHTML(student.contact_number)}</td>
                    <td>${escapeHTML(student.email)}</td>
                    <td>
                        <button class="btn btn-sm btn-primary edit-student" data-student-id="${escapeHTML(student.student_id)}">
                            Edit
                        </button>
                        <button class="btn btn-sm btn-danger delete-student" data-student-id="${escapeHTML(student.student_id)}">
                            Delete
                        </button>
                    </td>
                </tr>
            `).join('');

            // Attach event listeners to the new buttons
            attachStudentButtonListeners();
        }

        function attachStudentButtonListeners() {
            // Edit button listeners
            document.querySelectorAll('.edit-student').forEach(button => {
                button.addEventListener('click', function() {
                    const studentId = this.getAttribute('data-student-id');
                    const row = this.closest('tr');
                    const cells = row.cells;

                    // Fill the form with student data
                    document.getElementById('editStudentId').value = studentId;
                    document.getElementById('studentIdInput').value = studentId;
                    document.getElementById('studentIdInput').readOnly = true; // Make student ID read-only when editing
                    document.getElementById('fullName').value = cells[1].textContent;
                    document.getElementById('gender').value = cells[2].textContent;
                    document.getElementById('address').value = cells[3].textContent;
                    document.getElementById('contactNumber').value = cells[4].textContent;
                    document.getElementById('email').value = cells[5].textContent;
                    document.getElementById('password').value = ''; // Clear password field
                    document.getElementById('password').required = false; // Make password optional for edit

                    // Update form action and modal title
                    document.querySelector('#studentForm input[name="action"]').value = 'update';
                    document.getElementById('addStudentModalLabel').textContent = 'Edit Student';

                    // Show the modal
                    new bootstrap.Modal(document.getElementById('addStudentModal')).show();
                });
            });

            // Delete button listeners
            document.querySelectorAll('.delete-student').forEach(button => {
                button.addEventListener('click', function() {
                    if (confirm('Are you sure you want to delete this student?')) {
                        const studentId = this.getAttribute('data-student-id');
                        deleteStudent(studentId);
                    }
                });
            });
        }

        function deleteStudent(studentId) {
            fetch('student_operations.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'delete',
                    student_id: studentId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Student deleted successfully');
                    loadStudents(); // Reload the table
                } else {
                    alert('Error deleting student: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting student. Please try again.');
            });
        }

        // Initialize the page
        document.addEventListener('DOMContentLoaded', function() {
            // Get the stored section or default to profile
            const storedSection = sessionStorage.getItem('currentSection') || 'profile';
            switchSection(storedSection);

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

            // Handle semester selection
            document.querySelectorAll('input[name="semester"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    const semester = this.value;
                    fetchAndDisplaySemesterData(semester);
                });
            });

            // Initial fetch and display for the subjects section if it's the active section on load
            const initialSection = sessionStorage.getItem('currentSection') || 'profile';
            if (initialSection === 'subjects') {
                // Get the semester from the URL or default to 1st for initial load
                const urlParams = new URLSearchParams(window.location.search);
                const initialSemester = urlParams.get('semester') || '1st';
                // Check the corresponding radio button
                const initialRadio = document.querySelector(`input[name="semester"][value="${initialSemester}"]`);
                if (initialRadio) {
                    initialRadio.checked = true;
                }
                fetchAndDisplaySemesterData(initialSemester);
                 // Attach subjects button listeners on initial subjects load
                attachSubjectsButtonListeners();
            } else {
                // For non-subjects initial sections, still need to attach save listeners if subjects section is ever manually shown
                // This is less critical as we attach after rendering, but good practice.
                // attachSaveButtonListeners(); // Not strictly needed here as renderSubjectsContent calls it
            }

            // Attach save button listeners initially for the profile section form (if any)
            // and for the subjects section if it's the initial view (handled by fetchAndDisplaySemesterData -> renderSubjectsContent)
            attachSubjectsButtonListeners(); // Call once on DOMContentLoaded to cover initial profile form and potentially initial subjects render

             // When switching to subjects section, attach subjects button listeners
            document.querySelectorAll('.nav-link[data-section="subjects"]').forEach(link => {
                link.addEventListener('click', function() {
                     // Assuming fetchAndDisplaySemesterData is called when switching to subjects, 
                    // attachSubjectsButtonListeners will be called after rendering in that function.
                });
            });

            // Student Management Initialization
            const studentForm = document.getElementById('studentForm');
            const saveStudentBtn = document.getElementById('saveStudentBtn');
            const studentSearch = document.getElementById('studentSearch');

            if (saveStudentBtn) {
                saveStudentBtn.addEventListener('click', function() {
                    const formData = new FormData(studentForm);
                    const data = {
                        action: formData.get('action'),
                        student_id: formData.get('student_id'),
                        full_name: formData.get('full_name'),
                        gender: formData.get('gender'),
                        address: formData.get('address'),
                        contact_number: formData.get('contact_number'),
                        email: formData.get('email')
                    };

                    // Only include password if it's provided
                    const password = formData.get('password');
                    if (password) {
                        data.password = password;
                    }

                    // For add action, use the studentIdInput value
                    if (data.action === 'add') {
                        data.student_id = document.getElementById('studentIdInput').value;
                    }

                    fetch('student_operations.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Student ' + (formData.get('action') === 'add' ? 'added' : 'updated') + ' successfully!');
                            bootstrap.Modal.getInstance(document.getElementById('addStudentModal')).hide();
                            loadStudents(); // Reload the table
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error. Please try again.');
                    });
                });
            }

            // Search functionality
            if (studentSearch) {
                studentSearch.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    const rows = document.querySelectorAll('#studentsTableBody tr');
                    
                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        row.style.display = text.includes(searchTerm) ? '' : 'none';
                    });
                });
            }

            // Reset form when modal is closed
            const addStudentModal = document.getElementById('addStudentModal');
            if (addStudentModal) {
                addStudentModal.addEventListener('hidden.bs.modal', function() {
                    studentForm.reset();
                    document.getElementById('editStudentId').value = '';
                    document.getElementById('studentIdInput').readOnly = false; // Reset read-only state
                    document.getElementById('password').required = true;
                    document.querySelector('#studentForm input[name="action"]').value = 'add';
                    document.getElementById('addStudentModalLabel').textContent = 'Add New Student';
                });
            }

            // Load students when student management section is shown
            const studentManagementSection = document.getElementById('student-management');
            if (studentManagementSection) {
                const observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        if (mutation.target.style.display === 'block') {
                            loadStudents();
                        }
                    });
                });

                observer.observe(studentManagementSection, {
                    attributes: true,
                    attributeFilter: ['style']
                });
            }

            // Handle teacher profile form submission
            const teacherProfileForm = document.getElementById('teacherProfileForm');
            if (teacherProfileForm) {
                teacherProfileForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    const data = {
                        action: 'update',
                        username: '<?php echo htmlspecialchars($teacher['username']); ?>',
                        full_name: formData.get('fullName'),
                        sex: formData.get('sex'),
                        address: formData.get('address'),
                        contact_number: formData.get('contact'),
                        email: formData.get('email')
                    };

                    // Only include password if it's provided
                    const password = formData.get('password');
                    if (password) {
                        data.password = password;
                    }

                    fetch('teacher_operations.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Profile updated successfully!');
                            location.reload(); // Reload to show updated data
                        } else {
                            alert('Error updating profile: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error updating profile. Please try again.');
                    });
                });
            }
        });
    </script>
</body>
</html>
