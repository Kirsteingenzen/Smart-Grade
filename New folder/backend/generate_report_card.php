<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Get JSON data from request body
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON data: ' . json_last_error_msg()]);
    exit();
}

if (!isset($data['student_id'])) {
    echo json_encode(['success' => false, 'message' => 'Student ID is required']);
    exit();
}

// Database connection
try {
    $conn = new mysqli("localhost", "root", "", "smartgrade");

    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }

    $student_id = $data['student_id'];

    // Get only student name
    $stmt = $conn->prepare("SELECT full_name FROM students WHERE student_id = ?");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $student_id);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
    $stmt->close();

    if (!$student) {
        echo json_encode(['success' => false, 'message' => 'Student not found']);
        exit();
    }

    // Get grades for both semesters
    $stmt = $conn->prepare("
        SELECT s.subject_name, g.grade, g.semester 
        FROM grades g 
        JOIN subjects s ON g.subject_id = s.id 
        WHERE g.student_id = ? 
        ORDER BY g.semester, s.subject_name
    ");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $student_id);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    $result = $stmt->get_result();

    // Organize grades by semester and subject type
    $grades = [
        '1st' => [
            'Core Subjects' => [],
            'Applied and Specialized Subjects' => []
        ],
        '2nd' => [
            'Core Subjects' => [],
            'Applied and Specialized Subjects' => []
        ]
    ];

    while ($row = $result->fetch_assoc()) {
        $semester = $row['semester'];
        $subjectType = strpos($row['subject_name'], 'Empowerment') !== false || 
                      strpos($row['subject_name'], 'Pre-Calculus') !== false || 
                      strpos($row['subject_name'], 'General Chemistry') !== false 
                      ? 'Applied and Specialized Subjects' 
                      : 'Core Subjects';
        
        $grades[$semester][$subjectType][] = [
            'subject_name' => $row['subject_name'],
            'grade' => $row['grade']
        ];
    }

    $stmt->close();
    $conn->close();

    // Return success response with only student name and grades
    echo json_encode([
        'success' => true,
        'student' => ['full_name' => $student['full_name']],
        'grades' => $grades
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
    exit();
} 