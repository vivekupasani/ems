<?php
session_start();
require_once 'config.php';

$conn = connectDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employee_id = $_POST['employee_id'];
    $new_employee_code = $_POST['new_employee_code'];

    // Validate inputs
    if (empty($employee_id) || empty($new_employee_code)) {
        echo json_encode(['success' => false, 'message' => 'Invalid input']);
        exit;
    }

    // Update employee approval status and code
    $stmt = $conn->prepare("UPDATE employees SET 
        approval_status = 'approved', 
        emp_code = ? 
        WHERE id = ?");
    
    $stmt->bind_param("si", $new_employee_code, $employee_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Database update failed: ' . $stmt->error
        ]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
$conn->close();
?>