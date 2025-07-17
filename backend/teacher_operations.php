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
            $stmt = $pdo->prepare("INSERT INTO teachers (username, password, full_name, email, contact_number, department) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['username'],
                $_POST['password'],
                $_POST['full_name'],
                $_POST['email'] ?? null,
                $_POST['contact_number'] ?? null,
                $_POST['department'] ?? null
            ]);
            
            echo json_encode(['success' => true, 'message' => 'Teacher added successfully']);
        } catch(PDOException $e) {
            echo json_encode(['error' => 'Failed to add teacher: ' . $e->getMessage()]);
        }
        break;

    case 'edit':
        try {
            $stmt = $pdo->prepare("UPDATE teachers SET full_name = ?, email = ?, contact_number = ?, department = ? WHERE id = ?");
            $stmt->execute([
                $_POST['full_name'],
                $_POST['email'] ?? null,
                $_POST['contact_number'] ?? null,
                $_POST['department'] ?? null,
                $_POST['id']
            ]);
            
            echo json_encode(['success' => true, 'message' => 'Teacher updated successfully']);
        } catch(PDOException $e) {
            echo json_encode(['error' => 'Failed to update teacher: ' . $e->getMessage()]);
        }
        break;

    case 'delete':
        try {
            $stmt = $pdo->prepare("DELETE FROM teachers WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            
            echo json_encode(['success' => true, 'message' => 'Teacher deleted successfully']);
        } catch(PDOException $e) {
            echo json_encode(['error' => 'Failed to delete teacher: ' . $e->getMessage()]);
        }
        break;

    case 'get':
        try {
            $stmt = $pdo->prepare("SELECT * FROM teachers WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            $teacher = $stmt->fetch(PDO::FETCH_ASSOC);
            
            echo json_encode(['success' => true, 'teacher' => $teacher]);
        } catch(PDOException $e) {
            echo json_encode(['error' => 'Failed to get teacher: ' . $e->getMessage()]);
        }
        break;

    case 'list':
        try {
            $stmt = $pdo->query("SELECT * FROM teachers ORDER BY full_name");
            $teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode(['success' => true, 'teachers' => $teachers]);
        } catch(PDOException $e) {
            echo json_encode(['error' => 'Failed to list teachers: ' . $e->getMessage()]);
        }
        break;

    case 'login':
        try {
            $stmt = $pdo->prepare("SELECT * FROM teachers WHERE username = ? AND password = ?");
            $stmt->execute([$_POST['username'], $_POST['password']]);
            $teacher = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($teacher) {
                echo json_encode(['success' => true, 'teacher' => $teacher]);
            } else {
                echo json_encode(['error' => 'Invalid username or password']);
            }
        } catch(PDOException $e) {
            echo json_encode(['error' => 'Login failed: ' . $e->getMessage()]);
        }
        break;

    default:
        echo json_encode(['error' => 'Invalid action']);
} 