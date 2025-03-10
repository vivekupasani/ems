<?php
require_once 'config.php';

$conn = connectDB();

// Check if an employee ID is passed in the URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // If confirm is true, proceed with deletion logic
    if (isset($_GET['confirm']) && $_GET['confirm'] == 'true') {
        // Get leaving_date from URL if provided, otherwise check the database
        $leaving_date = isset($_GET['leaving_date']) ? $_GET['leaving_date'] : null;

        if (!$leaving_date) {
            // Check if leaving_date exists in the database
            $check_sql = "SELECT leaving_date FROM employees WHERE id = ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("i", $id);
            $check_stmt->execute();
            $result = $check_stmt->get_result();
            $employee = $result->fetch_assoc();
            $check_stmt->close();

            $leaving_date = $employee['leaving_date'];
        }

        // If leaving_date is still empty, prompt for it and redirect with the value
        if (empty($leaving_date)) {
            echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <script src='https://cdn.tailwindcss.com'></script>
</head>
<body>
    <script>
        var leavingDate = prompt('Please enter the leaving date (YYYY-MM-DD) for the employee:', '');
        if (leavingDate === null || leavingDate === '') {
            alert('Leaving date is required for deletion.');
            window.location.href = 'index.php#view-employees';
        } else {
            // Validate date format (basic check)
            var datePattern = /^\d{4}-\d{2}-\d{2}$/;
            if (!datePattern.test(leavingDate)) {
                alert('Invalid date format. Please use YYYY-MM-DD.');
                window.location.href = 'index.php#view-employees';
            } else {
                // Redirect with leaving_date included
                window.location.href = 'delete.php?id=" . $id . "&confirm=true&leaving_date=' + encodeURIComponent(leavingDate);
            }
        }
    </script>
</body>
</html>";
            $conn->close();
            exit;
        }

        // At this point, leaving_date is either from the database or URL
        $conn->begin_transaction();

        try {
            // If leaving_date was provided via URL, update the employees table
            if (isset($_GET['leaving_date'])) {
                $update_sql = "UPDATE employees SET leaving_date = ? WHERE id = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("si", $leaving_date, $id);
                if (!$update_stmt->execute()) {
                    throw new Exception("Error updating leaving_date: " . $update_stmt->error);
                }
                $update_stmt->close();
            }

            // Step 1: Copy the employee data to history_emp
            $insert_sql = "INSERT INTO history_emp 
                SELECT * FROM employees WHERE id = ?";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("i", $id);

            if (!$insert_stmt->execute()) {
                throw new Exception("Error inserting into history_emp: " . $insert_stmt->error);
            }

            $insert_stmt->close();

            // Step 2: Delete the employee from employees table
            $delete_sql = "DELETE FROM employees WHERE id = ?";
            $delete_stmt = $conn->prepare($delete_sql);
            $delete_stmt->bind_param("i", $id);

            if (!$delete_stmt->execute()) {
                throw new Exception("Error deleting employee: " . $delete_stmt->error);
            }

            // Commit the transaction
            $conn->commit();

            // Output success toast and redirect
            echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <script src='https://cdn.tailwindcss.com'></script>
    <style>
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #10b981;
            color: white;
            padding: 15px 20px;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }
        .toast.error {
            background-color: #ef4444;
        }
        .toast.show {
            opacity: 1;
        }
    </style>
</head>
<body>
    <script>
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.textContent = message;
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.classList.add('show');
            }, 100);

            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => {
                    document.body.removeChild(toast);
                }, 300);
            }, 5000);
        }

        // Show success toast and redirect
        showToast('Employee deleted and archived successfully!', 'success');
        setTimeout(() => {
            window.location.href = 'index.php#view-employees';
        }, 2000);
    </script>
</body>
</html>";

            $delete_stmt->close();
        } catch (Exception $e) {
            // Rollback the transaction if any operation fails
            $conn->rollback();

            // Output error toast
            echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <script src='https://cdn.tailwindcss.com'></script>
    <style>
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #10b981;
            color: white;
            padding: 15px 20px;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }
        .toast.error {
            background-color: #ef4444;
        }
        .toast.show {
            opacity: 1;
        }
    </style>
</head>
<body>
    <script>
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.textContent = message;
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.classList.add('show');
            }, 100);

            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => {
                    document.body.removeChild(toast);
                }, 300);
            }, 5000);
        }

        // Show error toast and redirect
        showToast('" . addslashes($e->getMessage()) . "', 'error');
        setTimeout(() => {
            window.location.href = 'index.php#view-employees';
        }, 2000);
    </script>
</body>
</html>";
        }
    } else {
        // Display confirmation dialog (initial access without confirm)
        echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <script src='https://cdn.tailwindcss.com'></script>
</head>
<body>
    <script>
        var result = confirm('Are you sure you want to delete this employee?');
        if (result) {
            window.location.href = 'delete.php?id=" . $id . "&confirm=true';
        } else {
            window.location.href = 'index.php#view-employees';
        }
    </script>
</body>
</html>";
    }
}

$conn->close();
?>