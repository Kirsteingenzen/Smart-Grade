<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Database connection
$host = 'localhost';
$dbname = 'smartgrade';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['action'] ?? '';

    switch ($action) {
        case 'update':
            if (!isset($data['grades']) || !is_array($data['grades'])) {
                throw new Exception('Invalid grades data');
            }

            $pdo->beginTransaction();

            try {
                foreach ($data['grades'] as $grade) {
                    if (!isset($grade['student_id']) || !isset($grade['subject_id']) || !isset($grade['grade']) || !isset($grade['semester'])) {
                        throw new Exception('Missing required fields');
                    }

                    // Check if grade exists
                    $stmt = $pdo->prepare("SELECT id FROM grades WHERE student_id = ? AND subject_id = ? AND semester = ?");
                    $stmt->execute([$grade['student_id'], $grade['subject_id'], $grade['semester']]);
                    $existingGrade = $stmt->fetch();

                    if ($existingGrade) {
                        // Update existing grade
                        $stmt = $pdo->prepare("UPDATE grades SET grade = ? WHERE id = ?");
                        $stmt->execute([$grade['grade'], $existingGrade['id']]);
                    } else {
                        // Insert new grade
                        $stmt = $pdo->prepare("INSERT INTO grades (student_id, subject_id, grade, semester) VALUES (?, ?, ?, ?)");
                        $stmt->execute([$grade['student_id'], $grade['subject_id'], $grade['grade'], $grade['semester']]);
                    }
                }

                $pdo->commit();
                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                $pdo->rollBack();
                throw $e;
            }
            break;

        default:
            throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 