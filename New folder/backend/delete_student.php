<?php
session_start();

// Check if user is logged in as teacher
if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Get JSON data from request
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['student_id'])) {
    echo json_encode(['success' => false, 'message' => 'Student ID is required']);
    exit;
}

$student_id = $data['student_id'];

// Database connection
$conn = new mysqli("localhost", "root", "", "smartgrade");

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// First delete related grades
$stmt = $conn->prepare("DELETE FROM grades WHERE student_id = ?");
$stmt->bind_param("s", $student_id);
$stmt->execute();

// Then delete the student
$stmt = $conn->prepare("DELETE FROM students WHERE student_id = ?");
$stmt->bind_param("s", $student_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Student deleted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete student']);
}

$stmt->close();
$conn->close();
?> 