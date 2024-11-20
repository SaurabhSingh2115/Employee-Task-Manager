<?php 
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id']) && $_SESSION['role'] == "admin") {
    include "DB_connection.php";
    include "app/Model/Task.php";
    include "app/Model/User.php";

    $text = "All Tasks";
    if (isset($_GET['due_date']) && $_GET['due_date'] == "Due Today") {
        $text = "Due Today";
        $tasks = get_all_tasks_due_today($conn);
        $num_task = count_tasks_due_today($conn);
    } else if (isset($_GET['due_date']) && $_GET['due_date'] == "Overdue") {
        $text = "Overdue";
        $tasks = get_all_tasks_overdue($conn);
        $num_task = count_tasks_overdue($conn);
    } else if (isset($_GET['due_date']) && $_GET['due_date'] == "No Deadline") {
        $text = "No Deadline";
        $tasks = get_all_tasks_NoDeadline($conn);
        $num_task = count_tasks_NoDeadline($conn);
    } else {
        $tasks = get_all_tasks($conn);
        $num_task = count_tasks($conn);
    }
    $users = get_all_users($conn);
?>
<!DOCTYPE html>
<html>
<head>
    <title>All Tasks</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <input type="checkbox" id="checkbox">
    <?php include "inc/header.php"; ?>
    <div class="body">
        <?php include "inc/nav.php"; ?>
        <section class="section-1">
            <h4 class="title-2">
                <a href="create_task.php" class="btn">Create Task</a>
                <a href="tasks.php?due_date=Due Today">Due Today</a>
                <a href="tasks.php?due_date=Overdue">Overdue</a>
                <a href="tasks.php?due_date=No Deadline">No Deadline</a>
                <a href="tasks.php">All Tasks</a>
            </h4>
            <h4 class="title-2"><?= $text ?> (<?= $num_task ?>)</h4>
            
            <?php if (isset($_GET['success'])) { ?>
                <div class="success" role="alert">
                    <?= stripcslashes($_GET['success']); ?>
                </div>
            <?php } ?>

            <?php if ($tasks != 0) { ?>
            <table class="main-table">
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Assigned To</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Attachment</th>
                    <th>Action</th>
                </tr>
                <?php $i = 0; foreach ($tasks as $task) { ?>
                <tr>
                    <td><?= ++$i ?></td>
                    <td><?= htmlspecialchars($task['title'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($task['description'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <?php 
                        foreach ($users as $user) {
                            if ($user['id'] == $task['assigned_to']) {
                                echo htmlspecialchars($user['full_name'], ENT_QUOTES, 'UTF-8');
                            }
                        } ?>
                    </td>
                    <td><?= $task['due_date'] ? $task['due_date'] : "No Deadline"; ?></td>
                    <td><?= htmlspecialchars($task['status'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <?php if (!empty($task['file_path'])) { ?>
                            <a href="download.php?file=<?= urlencode($task['file_path']) ?>" download>Download</a>

                        <?php } else { ?>
                            No File
                        <?php } ?>
                    </td>
                    <td>
                        <a href="edit-task.php?id=<?= $task['id'] ?>" class="edit-btn">Edit</a>
                        <a href="delete-task.php?id=<?= $task['id'] ?>" class="delete-btn">Delete</a>
                    </td>
                </tr>
                <?php } ?>
            </table>
            <?php } else { ?>
                <h3>Empty</h3>
            <?php } ?>
        </section>
    </div>

    <script type="text/javascript">
        var active = document.querySelector("#navList li:nth-child(4)");
        active.classList.add("active");
    </script>
</body>
</html>
<?php 
} else { 
    $em = "First login";
    header("Location: login.php?error=" . urlencode($em));
    exit();
}
?>
