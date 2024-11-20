<?php 
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id']) && $_SESSION['role'] == "admin") {
    include "DB_connection.php";
    include "app/Model/User.php";

    $users = get_all_users($conn); // Fetch all users
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Task</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <input type="checkbox" id="checkbox">
    <?php include "inc/header.php"; ?>
    <div class="body">
        <?php include "inc/nav.php"; ?>
        <section class="section-1">
            <h4 class="title">Create Task</h4>
            <form class="form-1" method="POST" action="app/add-task.php" enctype="multipart/form-data"> <!-- Added enctype -->
                <?php if (isset($_GET['error'])) { ?>
                    <div class="danger" role="alert">
                        <?php echo htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                <?php } ?>

                <?php if (isset($_GET['success'])) { ?>
                    <div class="success" role="alert">
                        <?php echo htmlspecialchars($_GET['success'], ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                <?php } ?>

                <!-- Task Title -->
                <div class="input-holder">
                    <label for="title">Title</label>
                    <input type="text" name="title" id="title" class="input-1" placeholder="Task Title" required>
                </div>

                <!-- Task Description -->
                <div class="input-holder">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" class="input-1" placeholder="Task Description" required></textarea>
                </div>

                <!-- Due Date -->
                <div class="input-holder">
                    <label for="due_date">Due Date</label>
                    <input type="date" name="due_date" id="due_date" class="input-1" required>
                </div>

                <!-- Assigned To -->
                <div class="input-holder">
                    <label for="assigned_to">Assigned To</label>
                    <select name="assigned_to" id="assigned_to" class="input-1" required>
                        <option value="0">Select employee</option>
                        <?php 
                        if ($users != 0) { 
                            foreach ($users as $user) { ?>
                                <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['full_name'], ENT_QUOTES, 'UTF-8'); ?></option>
                        <?php } } ?>
                    </select>
                </div>

                <!-- File Upload -->
                <div class="input-holder">
                    <label for="task_file">Upload File</label>
                    <input type="file" name="task_file" id="task_file" class="input-1" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.txt">
                </div>

                <!-- Submit Button -->
                <button class="edit-btn" type="submit">Create Task</button>
            </form>
        </section>
    </div>

    <script type="text/javascript">
        var active = document.querySelector("#navList li:nth-child(3)");
        active.classList.add("active");
    </script>
</body>
</html>
<?php 
} else { 
    $em = "Please log in first.";
    header("Location: login.php?error=" . urlencode($em));
    exit();
} 
?>
