<?php session_start(); ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<div class="container mt-5 col-md-4">
<div class="card p-4">
<h4>Login</h4>
<form method="POST" action="auth.php">
<input name="email" class="form-control mb-2" placeholder="Email">
<input type="password" name="password" class="form-control mb-2">
<button class="btn btn-primary w-100">Login</button>
</form>
</div></div>
