<?php
require_once 'config.php';

$conn = connectDB();

// Check if an employee ID is passed in the URL and if the deletion is confirmed
if (isset($_GET['id']) && isset($_GET['confirm']) && $_GET['confirm'] == 'true') {
    $id = $_GET['id'];
    
    // Proceed with the database deletion
    $sql = "DELETE FROM employees WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Display success message and redirect to the employee view page
        echo "<script>alert('Employee deleted successfully!'); window.location.href='index.php#view-employees';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else if (isset($_GET['id'])) {
    $id = $_GET['id'];
    // Display confirmation dialog
    echo "<script>
        var result = confirm('Are you sure you want to delete this employee?');
        if (result) {
            window.location.href = 'delete.php?id=" . $id . "&confirm=true';
        } else {
            window.location.href = 'index.php#view-employees';
        }
    </script>";
}

$conn->close();
?>
