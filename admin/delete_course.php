<?php
require '../connection/db.php';
session_start();

// Only allow admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = "Access denied!";
    header("Location: index.php");
    exit();
}

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    $_SESSION['error'] = "Invalid course ID!";
    header("Location: admin_dashboard.php");
    exit();
}

// Check if course exists in my_courses
$stmt = $conn->prepare("SELECT COUNT(*) FROM my_courses WHERE course_id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($count);
$stmt->fetch();
$stmt->close();

if ($count > 0) {
    $_SESSION['error'] = "Cannot delete this course because students have enrolled in it!";
    header("Location: admin_dashboard.php");
    exit();
}

// Delete course
$stmt = $conn->prepare("DELETE FROM courses WHERE id=?");
$stmt->bind_param("i", $id);
if ($stmt->execute()) {
    $_SESSION['success'] = "Course deleted successfully!";
} else {
    $_SESSION['error'] = "Database error: " . $stmt->error;
}
$stmt->close();
header("Location: admin_dashboard.php");
exit();