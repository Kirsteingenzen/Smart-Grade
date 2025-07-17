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
    
    if (!isset($data['grade_id'])) {
        throw new Exception('Missing grade ID');
    }

    // Delete grade from database
    $stmt = $pdo->prepare("DELETE FROM grades WHERE id = ?");
    $result = $stmt->execute([$data['grade_id']]);

    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        throw new Exception('Failed to delete grade');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 