<?php 
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id'])) {
    include "DB_connection.php";
    include "app/Model/Task.php";
    include "app/Model/User.php";

    // Fetch all tasks assigned to the logged-in user
    $tasks = get_all_tasks_by_id($conn, $_SESSION['id']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Tasks</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <input type="checkbox" id="checkbox">
    <?php include "inc/header.php"; ?>
    <div class="body">
        <?php include "inc/nav.php"; ?>
        <section class="section-1">
            <h4 class="title">My Tasks</h4>
            
            <!-- Success Message -->
            <?php if (isset($_GET['success'])) { ?>
                <div class="success" role="alert">
                    <?php echo stripcslashes($_GET['success']); ?>
                </div>
            <?php } ?>

            <!-- Tasks Table -->
            <?php if ($tasks != 0) { ?>
            <table class="main-table">
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Due Date</th>
                    <th>Attachment</th>
                    <th>Action</th>
                </tr>
                <?php $i = 0; foreach ($tasks as $task) { ?>
                <tr>
                    <td><?= ++$i ?></td>
                    <td><?= htmlspecialchars($task['title'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($task['description'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($task['status'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= $task['due_date'] ? $task['due_date'] : "No Deadline" ?></td>
                    <td>
                        <?php if (!empty($task['file_path'])) { ?>
                            <a href="download.php?file=<?= urlencode($task['file_path']) ?>" download>Download</a>

                        <?php } else { ?>
                            No File
                        <?php } ?>
                    </td>
                    <td>
                        <a href="edit-task-employee.php?id=<?= $task['id'] ?>" class="edit-btn">Edit</a>
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
        var active = document.querySelector("#navList li:nth-child(2)");
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
