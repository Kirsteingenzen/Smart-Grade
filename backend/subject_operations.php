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
            $stmt = $pdo->prepare("INSERT INTO subjects (subject_name) VALUES (?)");
            $stmt->execute([$_POST['subject_name']]);
            
            echo json_encode(['success' => true, 'message' => 'Subject added successfully']);
        } catch(PDOException $e) {
            echo json_encode(['error' => 'Failed to add subject: ' . $e->getMessage()]);
        }
        break;

    case 'edit':
        try {
            $stmt = $pdo->prepare("UPDATE subjects SET subject_name = ? WHERE id = ?");
            $stmt->execute([
                $_POST['subject_name'],
                $_POST['id']
            ]);
            
            echo json_encode(['success' => true, 'message' => 'Subject updated successfully']);
        } catch(PDOException $e) {
            echo json_encode(['error' => 'Failed to update subject: ' . $e->getMessage()]);
        }
        break;

    case 'delete':
        try {
            $stmt = $pdo->prepare("DELETE FROM subjects WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            
            echo json_encode(['success' => true, 'message' => 'Subject deleted successfully']);
        } catch(PDOException $e) {
            echo json_encode(['error' => 'Failed to delete subject: ' . $e->getMessage()]);
        }
        break;

    case 'get':
        try {
            $stmt = $pdo->prepare("SELECT * FROM subjects WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            $subject = $stmt->fetch(PDO::FETCH_ASSOC);
            
            echo json_encode(['success' => true, 'subject' => $subject]);
        } catch(PDOException $e) {
            echo json_encode(['error' => 'Failed to get subject: ' . $e->getMessage()]);
        }
        break;

    case 'list':
        try {
            $stmt = $pdo->query("SELECT * FROM subjects ORDER BY subject_name");
            $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode(['success' => true, 'subjects' => $subjects]);
        } catch(PDOException $e) {
            echo json_encode(['error' => 'Failed to list subjects: ' . $e->getMessage()]);
        }
        break;

    default:
        echo json_encode(['error' => 'Invalid action']);
} 