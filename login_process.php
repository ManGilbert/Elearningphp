<?php
require 'connection/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Check if email or password is empty
    if ($email === '' || $password === '') {
        $_SESSION['error'] = "Please enter both email and password!";
        header("Location: index.php");
        exit();
    }

    // Prepare SQL to prevent SQL injection
    $stmt = $conn->prepare("SELECT id, full_name, password, role, status FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $full_name, $db_password, $role, $status);
        $stmt->fetch();

        // Check if user is blocked
        if ($status === 'blocked') {
            $_SESSION['error'] = "Your account is blocked! Please contact admin.";
            $stmt->close();
            header("Location: index.php");
            exit();
        }

        // Direct comparison since passwords are plain text
        if ($password === $db_password) {
            $_SESSION['user_id'] = $id;
            $_SESSION['full_name'] = $full_name;
            $_SESSION['role'] = $role;
            $stmt->close();

            // Redirect based on role
            if ($role === 'admin') {
                header("Location: admin/admin_dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            $_SESSION['error'] = "Incorrect password!";
        }
    } else {
        $_SESSION['error'] = "Email not found!";
    }

    $stmt->close();
    header("Location: index.php");
    exit();
}