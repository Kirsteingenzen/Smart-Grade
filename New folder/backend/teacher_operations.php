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

    // Get the request body
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['action'] ?? '';

    switch ($action) {
        case 'update':
            // Update teacher profile
            $stmt = $pdo->prepare("UPDATE teachers SET full_name = ?, sex = ?, address = ?, contact_number = ?, email = ? WHERE username = ?");
            $stmt->execute([
                $data['full_name'],
                $data['sex'],
                $data['address'],
                $data['contact_number'],
                $data['email'],
                $data['username']
            ]);

            // If password is provided, update it
            if (!empty($data['password'])) {
                $stmt = $pdo->prepare("UPDATE teachers SET password = ? WHERE username = ?");
                $stmt->execute([$data['password'], $data['username']]);
            }
            echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?> 