<?php
require '../connection/db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = "Access denied!";
    header("Location: index.php");
    exit();
}

$id = intval($_GET['id'] ?? 0);
$action = $_GET['action'] ?? '';

if ($id <= 0 || !in_array($action, ['block','unblock'])) {
    $_SESSION['error'] = "Invalid request!";
    header("Location: user_manage.php");
    exit();
}

$status = $action === 'block' ? 'blocked' : 'active';
$stmt = $conn->prepare("UPDATE users SET status=? WHERE id=?");
$stmt->bind_param("si", $status, $id);
if ($stmt->execute()) {
    $_SESSION['success'] = "User status updated!";
} else {
    $_SESSION['error'] = "Database error: ".$stmt->error;
}
$stmt->close();

header("Location: user_manage.php");
exit();