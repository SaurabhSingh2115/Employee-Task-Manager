<?php
session_start();
if (isset($_SESSION['role']) && $_SESSION['role'] == "admin") {
    include "DB_connection.php";
    include "app/Model/Task.php";

    $user_points = calculate_user_points($conn);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Performance Analysis</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <input type="checkbox" id="checkbox">
    <?php include "inc/header.php"; ?>
    <div class="body">
        <?php include "inc/nav.php"; ?>
        <section class="section-1">
            <h4 class="title-2">Performance Analysis</h4>
            <table class="main-table">
                <tr>
                    <th>#</th>
                    <th>Employee Name</th>
                    <th>Points</th>
                </tr>
                <?php
                if (count($user_points) > 0) {
                    $rank = 1;
                    foreach ($user_points as $user) { ?>
                        <tr>
                            <td><?= $rank++ ?></td>
                            <td><?= htmlspecialchars($user['full_name'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= $user['points'] ?></td>
                        </tr>
                <?php } 
                } else { ?>
                    <tr>
                        <td colspan="3">No data available.</td>
                    </tr>
                <?php } ?>
            </table>
        </section>
    </div>
</body>
</html>
<?php
} else {
    $em = "Unauthorized access";
    header("Location: login.php?error=" . urlencode($em));
    exit();
}
?>
