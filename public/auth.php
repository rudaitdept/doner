<?php
session_start(); include '../config/db.php';
$email=$_POST['email']; $pass=$_POST['password'];
$q=$conn->query("SELECT * FROM users WHERE email='$email'");
$u=$q->fetch_assoc();
if($u && password_verify($pass,$u['password'])){
$_SESSION['user']=$u; header("Location:index.php");
}else echo "Login Failed";
?>