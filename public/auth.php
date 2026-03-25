<?php
session_start();
include '../config/db.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit;
}

$email = $_POST['email'] ?? '';
$pass = $_POST['password'] ?? '';

// Use prepared statement to prevent SQL injection
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if($user && password_verify($pass, $user['password'])) {
    $_SESSION['user'] = $user;
    header("Location: index.php");
    exit;
} else {
    header("Location: login.php?error=1");
    exit;
}
?>
