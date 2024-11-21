<?php
include "DB_connection.php";
include "app/Model/Task.php";

if (isset($_POST['task_id'])) {
    $task_id = $_POST['task_id'];
    mark_task_completed($conn, $task_id);
    header("Location: tasks.php?success=Task marked as completed.");
    exit();
} else {
    header("Location: tasks.php?error=Invalid task ID.");
    exit();
}
?>
