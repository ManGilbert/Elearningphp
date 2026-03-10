<?php
require 'connection/db.php';
session_start();

// Only logged-in students can enroll
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Please log in to enroll in courses!";
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$course_id = intval($_GET['course_id'] ?? 0);

if ($course_id <= 0) {
    $_SESSION['error'] = "Invalid course ID!";
    header("Location: index.php");
    exit();
}

// Check if course exists
$stmt = $conn->prepare("SELECT id, name FROM courses WHERE id=?");
$stmt->bind_param("i", $course_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows !== 1) {
    $_SESSION['error'] = "Course not found!";
    $stmt->close();
    header("Location: index.php");
    exit();
}

$stmt->bind_result($id, $course_name);
$stmt->fetch();
$stmt->close();

// Check if already enrolled
$stmt = $conn->prepare("SELECT id FROM my_courses WHERE user_id=? AND course_id=?");
$stmt->bind_param("ii", $user_id, $course_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $_SESSION['error'] = "You are already enrolled in '{$course_name}'!";
    $stmt->close();
    header("Location: my_courses.php");
    exit();
}
$stmt->close();

// Enroll student
$stmt = $conn->prepare("INSERT INTO my_courses (user_id, course_id, progress) VALUES (?, ?, 0)");
$stmt->bind_param("ii", $user_id, $course_id);

if ($stmt->execute()) {
    $_SESSION['success'] = "Successfully enrolled in '{$course_name}'!";
} else {
    $_SESSION['error'] = "Database error: " . $stmt->error;
}

$stmt->close();
header("Location: my_courses.php");
exit();