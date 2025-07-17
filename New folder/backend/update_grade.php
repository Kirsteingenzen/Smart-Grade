<?php
header('Content-Type: application/json');

// Database connection parameters
$host = 'localhost';
$dbname = 'smartgrade';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['grades']) || !is_array($data['grades'])) {
        throw new Exception('Invalid data format');
    }

    $pdo->beginTransaction();

    foreach ($data['grades'] as $gradeData) {
        if (!isset($gradeData['grade']) || !isset($gradeData['subject']) || !isset($gradeData['student_id'])) {
            throw new Exception('Missing required fields');
        }

        // Validate grade value
        $grade = floatval($gradeData['grade']);
        if ($grade < 0 || $grade > 100) {
            throw new Exception('Grade must be between 0 and 100');
        }

        if (empty($gradeData['grade_id'])) {
            // Insert new grade
            $stmt = $pdo->prepare("
                INSERT INTO grades (student_id, subject, grade, semester) 
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([
                $gradeData['student_id'],
                $gradeData['subject'],
                $grade,
                $gradeData['semester'] ?? '1st'
            ]);
        } else {
            // Update existing grade
            $stmt = $pdo->prepare("UPDATE grades SET grade = ? WHERE id = ?");
            $stmt->execute([$grade, $gradeData['grade_id']]);
        }
    }

    $pdo->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    if (isset($pdo)) {
        $pdo->rollBack();
    }
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 