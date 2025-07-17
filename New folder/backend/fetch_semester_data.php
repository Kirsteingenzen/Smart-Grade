<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Enable error reporting for debugging (optional, disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Database connection parameters
$host = 'localhost';
$dbname = 'smartgrade';
$user = 'root';
$pass = '';

$response = ['success' => false, 'message' => '', 'data' => []];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get the selected semester from GET parameter
    $selectedSemester = isset($_GET['semester']) ? $_GET['semester'] : '1st';

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

    // Separate processed students by gender and convert to indexed arrays for JSON
    $maleStudents = array_values(array_filter($processedStudents, function($student) {
        return $student['gender'] === 'Male';
    }));
    $femaleStudents = array_values(array_filter($processedStudents, function($student) {
        return $student['gender'] === 'Female';
    }));

    $response['success'] = true;
    $response['data'] = [
        'subjects' => $subjects,
        'maleStudents' => $maleStudents,
        'femaleStudents' => $femaleStudents
    ];

} catch (PDOException $e) {
    $response['message'] = 'Database error: ' . $e->getMessage();
    http_response_code(500);
} catch (Exception $e) {
     $response['message'] = 'Error: ' . $e->getMessage();
     http_response_code(400);
}

echo json_encode($response);
?> 