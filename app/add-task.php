<?php 
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id'])) {

if (isset($_POST['title']) && isset($_POST['description']) && isset($_POST['assigned_to']) && $_SESSION['role'] == 'admin' && isset($_POST['due_date'])) {
    include "../DB_connection.php";

    function validate_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $title = validate_input($_POST['title']);
    $description = validate_input($_POST['description']);
    $assigned_to = validate_input($_POST['assigned_to']);
    $due_date = validate_input($_POST['due_date']);
    $file_path = null; // Default value if no file is uploaded

    // Validate form fields
    if (empty($title)) {
        $em = "Title is required";
        header("Location: ../create_task.php?error=$em");
        exit();
    } else if (empty($description)) {
        $em = "Description is required";
        header("Location: ../create_task.php?error=$em");
        exit();
    } else if ($assigned_to == 0) {
        $em = "Select User";
        header("Location: ../create_task.php?error=$em");
        exit();
    } else {
        // Handle file upload
        if (isset($_FILES['task_file']) && $_FILES['task_file']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = "../uploads/"; // Directory to store uploads
            $file_name = uniqid() . "_" . basename($_FILES['task_file']['name']);
            $file_path = $upload_dir . $file_name;

            // Ensure the uploads directory exists
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            // Move the uploaded file to the uploads directory
            if (!move_uploaded_file($_FILES['task_file']['tmp_name'], $file_path)) {
                $em = "Failed to upload file.";
                header("Location: ../create_task.php?error=$em");
                exit();
            }
        }

        // Insert task into the database
        include "Model/Task.php";
        include "Model/Notification.php";

        $data = array($title, $description, $assigned_to, $due_date, $file_path);
        insert_task($conn, $data);

        // Create notification for the assigned user
        $notif_data = array("'$title' has been assigned to you. Please review and start working on it", $assigned_to, 'New Task Assigned');
        insert_notification($conn, $notif_data);

        // Redirect with success message
        $em = "Task created successfully";
        header("Location: ../create_task.php?success=$em");
        exit();
    }
} else {
    $em = "Unknown error occurred";
    header("Location: ../create_task.php?error=$em");
    exit();
}

} else { 
    $em = "First login";
    header("Location: ../create_task.php?error=$em");
    exit();
}
?>
