<?php
require '../connection/db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = "Access denied!";
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $youtube_link = trim($_POST['youtube_link'] ?? '');
    $created_by = $_SESSION['user_id'];

    if ($name === '' || $description === '') {
        $_SESSION['error'] = "Course name and description are required!";
        header("Location: admin_dashboard.php");
        exit();
    }

    // Handle image upload
    $image_name = null;
    if (!empty($_FILES['image']['name'])) {
        $allowed = ['jpg','jpeg','png','gif'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $image_name = uniqid() . "." . $ext;
            $upload_path = "../uploads/" . $image_name;
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $_SESSION['error'] = "Failed to upload image!";
                header("Location: admin_dashboard.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "Invalid image format! Allowed: jpg, jpeg, png, gif.";
            header("Location: admin_dashboard.php");
            exit();
        }
    }

    $stmt = $conn->prepare("INSERT INTO courses (name, description, category, youtube_link, image_url, created_by) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssi", $name, $description, $category, $youtube_link, $image_name, $created_by);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Course added successfully!";
    } else {
        $_SESSION['error'] = "Database error: " . $stmt->error;
    }
    $stmt->close();
    header("Location: admin_dashboard.php");
    exit();
}