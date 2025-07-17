<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Database connection parameters
$host = 'localhost';
$dbname = 'smartgrade';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get the POST data
    $rawData = file_get_contents('php://input');
    $data = json_decode($rawData, true);
    
    if (!$data) {
        throw new Exception('Invalid JSON data received: ' . $rawData);
    }
    
    if (!isset($data['action'])) {
        throw new Exception('No action specified');
    }

    switch ($data['action']) {
        case 'update':
            if (!isset($data['grades']) || !is_array($data['grades'])) {
                throw new Exception('No grades provided');
            }

            // Start transaction
            $pdo->beginTransaction();

            try {
                foreach ($data['grades'] as $grade) {
                    if (!isset($grade['student_id']) || !isset($grade['subject_id']) || !isset($grade['semester'])) {
                        throw new Exception('Missing required grade information');
                    }

                    // Log the grade data being processed
                    error_log('Processing grade data: ' . print_r($grade, true));

                    // Validate grade value
                    $gradeValue = isset($grade['grade']) && $grade['grade'] !== '' ? floatval($grade['grade']) : 0;
                    
                    if ($gradeValue !== null && ($gradeValue < 0 || $gradeValue > 100)) {
                        throw new Exception('Grade must be between 0 and 100');
                    }

                    // Use INSERT ... ON DUPLICATE KEY UPDATE to handle both insert and update
                    $stmt = $pdo->prepare("
                        INSERT INTO grades (student_id, subject_id, grade, semester)
                        VALUES (?, ?, ?, ?)
                        ON DUPLICATE KEY UPDATE
                        grade = VALUES(grade)
                    ");
                    $result = $stmt->execute([$grade['student_id'], $grade['subject_id'], $gradeValue, $grade['semester']]);

                    if (!$result) {
                        // Log the error details
                        error_log('Database operation failed: ' . implode(', ', $stmt->errorInfo()) . ' for data: ' . print_r($grade, true));
                        throw new Exception('Failed to save grade for student ' . $grade['student_id'] . ', subject ' . $grade['subject_id'] . ': ' . implode(', ', $stmt->errorInfo()));
                    }
                }

                // Commit transaction
                $pdo->commit();
                echo json_encode(['success' => true, 'message' => 'Grades updated successfully']);
            } catch (Exception $e) {
                // Rollback transaction on error
                $pdo->rollBack();
                throw new Exception('Database error: ' . $e->getMessage());
            }
            break;

        default:
            throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    error_log('Grade operation error: ' . $e->getMessage());
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} 