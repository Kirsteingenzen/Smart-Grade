<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'config.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die(json_encode(['error' => 'Connection failed: ' . $e->getMessage()]));
}

$action = $_POST['action'] ?? '';

switch($action) {
    case 'add':
        try {
            // Generate student ID (format: YYYY-XXXXX where X is random)
            $year = date('Y');
            $random = str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
            $student_id = $year . '-' . $random;
            
            $stmt = $pdo->prepare("INSERT INTO students (student_id, full_name, gender, contact_number) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $student_id,
                $_POST['fullName'],
                $_POST['gender'],
                $_POST['contactNumber']
            ]);
            
            echo json_encode(['success' => true, 'message' => 'Student added successfully', 'student_id' => $student_id]);
        } catch(PDOException $e) {
            echo json_encode(['error' => 'Failed to add student: ' . $e->getMessage()]);
        }
        break;

    case 'edit':
        try {
            $stmt = $pdo->prepare("UPDATE students SET full_name = ?, gender = ?, contact_number = ? WHERE student_id = ?");
            $stmt->execute([
                $_POST['fullName'],
                $_POST['gender'],
                $_POST['contactNumber'],
                $_POST['student_id']
            ]);
            
            echo json_encode(['success' => true, 'message' => 'Student updated successfully']);
        } catch(PDOException $e) {
            echo json_encode(['error' => 'Failed to update student: ' . $e->getMessage()]);
        }
        break;

    case 'delete':
        try {
            $stmt = $pdo->prepare("DELETE FROM students WHERE student_id = ?");
            $stmt->execute([$_POST['student_id']]);
            
            echo json_encode(['success' => true, 'message' => 'Student deleted successfully']);
        } catch(PDOException $e) {
            echo json_encode(['error' => 'Failed to delete student: ' . $e->getMessage()]);
        }
        break;

    case 'get':
        try {
            $stmt = $pdo->prepare("SELECT * FROM students WHERE student_id = ?");
            $stmt->execute([$_POST['student_id']]);
            $student = $stmt->fetch(PDO::FETCH_ASSOC);
            
            echo json_encode(['success' => true, 'student' => $student]);
        } catch(PDOException $e) {
            echo json_encode(['error' => 'Failed to get student: ' . $e->getMessage()]);
        }
        break;

    default:
        echo json_encode(['error' => 'Invalid action']);
} 