<?php
require 'connection/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // 1. Check if fields are empty
    if ($full_name === '' || $email === '' || $password === '' || $confirm_password === '') {
        $_SESSION['error'] = "Please fill in all fields!";
        header("Location: index.php");
        exit();
    }

    // 2. Check if passwords match
    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match!";
        header("Location: index.php");
        exit();
    }

    // 3. Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->close();
        $_SESSION['error'] = "Email already registered!";
        header("Location: index.php");
        exit();
    }
    $stmt->close();

    // 4. Insert new user with plain text password
    $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, role, status) VALUES (?, ?, ?, 'student', 'active')");
    $stmt->bind_param("sss", $full_name, $email, $password);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Signup successful! You can log in now.";
    } else {
        $_SESSION['error'] = "Database error: " . $stmt->error;
    }

    $stmt->close();
    header("Location: index.php");
    exit();
}