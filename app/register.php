<?php
session_start();
include "../DB_connection.php";

if (isset($_POST['full_name']) && isset($_POST['user_name']) && isset($_POST['password']) && isset($_POST['confirm_password'])) {
    function validate_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $full_name = validate_input($_POST['full_name']);
    $user_name = validate_input($_POST['user_name']);
    $password = validate_input($_POST['password']);
    $confirm_password = validate_input($_POST['confirm_password']);

    if (empty($full_name) || empty($user_name) || empty($password) || empty($confirm_password)) {
        $em = "All fields are required";
        header("Location: ../register.php?error=$em");
        exit();
    } else if ($password !== $confirm_password) {
        $em = "Passwords do not match";
        header("Location: ../register.php?error=$em");
        exit();
    } else {
        // Check if username already exists
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$user_name]);

        if ($stmt->rowCount() > 0) {
            $em = "Username already exists";
            header("Location: ../register.php?error=$em");
            exit();
        } else {
            // Hash password and insert user into database
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (full_name, username, password, role) VALUES (?, ?, ?, 'employee')";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$full_name, $user_name, $passwordHash]);

            $sm = "Registration successful! You can now log in.";
            header("Location: ../login.php?success=$sm");
            exit();
        }
    }
} else {
    $em = "Unknown error occurred";
    header("Location: ../register.php?error=$em");
    exit();
}
