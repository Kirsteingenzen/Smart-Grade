<?php
// Set headers to allow cross-origin requests and specify JSON content type
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Database connection parameters
$host = 'localhost';
$dbname = 'smartgrade';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get the request body
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['action'] ?? '';

    switch ($action) {
        case 'add':
            // Add new student
            $stmt = $pdo->prepare("INSERT INTO students (student_id, full_name, gender, address, contact_number, email, password) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['student_id'],
                $data['full_name'],
                $data['gender'],
                $data['address'],
                $data['contact_number'],
                $data['email'],
                $data['password']
            ]);
            echo json_encode(['success' => true, 'message' => 'Student added successfully']);
            break;

        case 'update':
            // Update existing student
            $stmt = $pdo->prepare("UPDATE students SET full_name = ?, gender = ?, address = ?, contact_number = ?, email = ? WHERE student_id = ?");
            $stmt->execute([
                $data['full_name'],
                $data['gender'],
                $data['address'],
                $data['contact_number'],
                $data['email'],
                $data['student_id']
            ]);

            // If password is provided, update it
            if (!empty($data['password'])) {
                $stmt = $pdo->prepare("UPDATE students SET password = ? WHERE student_id = ?");
                $stmt->execute([$data['password'], $data['student_id']]);
            }
            echo json_encode(['success' => true, 'message' => 'Student updated successfully']);
            break;

        case 'delete':
            // Delete student
            $stmt = $pdo->prepare("DELETE FROM students WHERE student_id = ?");
            $stmt->execute([$data['student_id']]);
            echo json_encode(['success' => true, 'message' => 'Student deleted successfully']);
            break;

        case 'get':
            // Get all students
            $stmt = $pdo->query("SELECT student_id, full_name, gender, address, contact_number, email FROM students ORDER BY full_name");
            $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $students]);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?> 