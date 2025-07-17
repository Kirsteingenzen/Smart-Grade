<?php
session_start();

// Database connection
$host = 'localhost';
$dbname = 'smartgrade';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get parameters
    $subject = $_GET['subject'] ?? '';
    $semester = $_GET['semester'] ?? '1st';

    // Get male students
    $stmt = $pdo->prepare("
        SELECT s.full_name, g.grade 
        FROM students s 
        JOIN grades g ON s.student_id = g.student_id 
        WHERE s.sex = 'Male' 
        AND g.semester = ? 
        AND g.subject = ?
        ORDER BY s.full_name
    ");
    $stmt->execute([$semester, $subject]);
    $maleStudents = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get female students
    $stmt = $pdo->prepare("
        SELECT s.full_name, g.grade 
        FROM students s 
        JOIN grades g ON s.student_id = g.student_id 
        WHERE s.sex = 'Female' 
        AND g.semester = ? 
        AND g.subject = ?
        ORDER BY s.full_name
    ");
    $stmt->execute([$semester, $subject]);
    $femaleStudents = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode([
        'male' => $maleStudents,
        'female' => $femaleStudents
    ]);

} catch (PDOException $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?> 