<?php
require '../connection/db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = "Access denied!";
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $youtube_link = trim($_POST['youtube_link'] ?? '');

    if ($id <= 0 || $name === '' || $description === '') {
        $_SESSION['error'] = "Invalid data provided!";
        header("Location: ../admin/admin_dashboard.php");
        exit();
    }

    // Handle image upload
    $image_sql = "";
    if (!empty($_FILES['image']['name'])) {
        $allowed = ['jpg','jpeg','png','gif'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $image_name = uniqid() . "." . $ext;
            $upload_path = "../uploads/" . $image_name;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image_sql = ", image_url='" . $conn->real_escape_string($image_name) . "'";
            } else {
                $_SESSION['error'] = "Failed to upload image!";
                header("Location: ../admin/admin_dashboard.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "Invalid image format! Allowed: jpg, jpeg, png, gif.";
            header("Location: admin_dashboard.php");
            exit();
        }
    }

    $sql = "UPDATE courses SET 
                name=?, 
                description=?, 
                category=?, 
                youtube_link=? 
                $image_sql
            WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $name, $description, $category, $youtube_link, $id);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Course updated successfully!";
    } else {
        $_SESSION['error'] = "Database error: " . $stmt->error;
    }
    $stmt->close();
    header("Location: admin_dashboard.php");
    exit();
}