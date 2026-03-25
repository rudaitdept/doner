<?php session_start(); 
if(isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Donation CRM</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card animate-fade-in">
            <div class="login-header">
                <div class="login-logo">D</div>
                <h1 class="login-title">Welcome Back</h1>
                <p class="login-subtitle">Sign in to your Donation CRM account</p>
            </div>
            
            <?php if(isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" x2="12" y1="8" y2="12"/>
                    <line x1="12" x2="12.01" y1="16" y2="16"/>
                </svg>
                Invalid email or password
            </div>
            <?php endif; ?>
            
            <form method="POST" action="auth.php" class="login-form">
                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email"
                        class="form-control" 
                        placeholder="Enter your email"
                        required
                        autocomplete="email"
                    >
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input 
                        type="password" 
                        name="password" 
                        id="password"
                        class="form-control" 
                        placeholder="Enter your password"
                        required
                        autocomplete="current-password"
                    >
                </div>
                
                <button type="submit" class="btn btn-primary btn-lg w-full">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                        <polyline points="10 17 15 12 10 7"/>
                        <line x1="15" x2="3" y1="12" y2="12"/>
                    </svg>
                    Sign In
                </button>
            </form>
            
            <div class="login-footer">
                <p>Donation CRM &copy; <?= date('Y') ?></p>
            </div>
        </div>
    </div>
</body>
</html>
