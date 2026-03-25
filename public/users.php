<?php 
include '../config/db.php'; 
include 'layout/header.php';

if(!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

if($_SESSION['user']['role'] != 'super_admin'){
    echo '<div class="alert alert-danger">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" x2="12" y1="8" y2="12"/>
            <line x1="12" x2="12.01" y1="16" y2="16"/>
        </svg>
        Access Denied: Only Super Admin can manage users
    </div>';
    include 'layout/footer.php';
    exit;
}

$success = false;
$error = '';

// Handle form submission
if($_POST){
    if(empty($_POST['name']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['role'])) {
        $error = 'All fields are required';
    } else {
        // Check if email already exists
        $checkEmail = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $checkEmail->bind_param("s", $_POST['email']);
        $checkEmail->execute();
        $emailResult = $checkEmail->get_result();
        
        if($emailResult->num_rows > 0) {
            $error = 'Email already exists';
        } else {
            $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users(name, email, password, role) VALUES(?, ?, ?, ?)");
            $stmt->bind_param("ssss", $_POST['name'], $_POST['email'], $pass, $_POST['role']);
            if($stmt->execute()) {
                $success = true;
            } else {
                $error = 'Failed to add user';
            }
        }
    }
}

// Get all users
$res = $conn->query("SELECT * FROM users ORDER BY id DESC");

// Get stats
$totalUsers = $conn->query("SELECT COUNT(*) c FROM users")->fetch_assoc()['c'];
$superAdmins = $conn->query("SELECT COUNT(*) c FROM users WHERE role='super_admin'")->fetch_assoc()['c'];
$supervisors = $conn->query("SELECT COUNT(*) c FROM users WHERE role='supervisor'")->fetch_assoc()['c'];
?>

<div class="page-header">
    <h1 class="page-title">User Management</h1>
    <p class="page-description">Add and manage system users</p>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon primary">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
        </div>
        <div class="stat-content">
            <p class="stat-label">Total Users</p>
            <p class="stat-value"><?= $totalUsers ?></p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon danger">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            </svg>
        </div>
        <div class="stat-content">
            <p class="stat-label">Super Admins</p>
            <p class="stat-value"><?= $superAdmins ?></p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon success">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
            </svg>
        </div>
        <div class="stat-content">
            <p class="stat-label">Supervisors</p>
            <p class="stat-value"><?= $supervisors ?></p>
        </div>
    </div>
</div>

<?php if($success): ?>
<div class="alert alert-success">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
        <polyline points="22 4 12 14.01 9 11.01"/>
    </svg>
    User added successfully!
</div>
<?php endif; ?>

<?php if($error): ?>
<div class="alert alert-danger">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="10"/>
        <line x1="12" x2="12" y1="8" y2="12"/>
        <line x1="12" x2="12.01" y1="16" y2="16"/>
    </svg>
    <?= htmlspecialchars($error) ?>
</div>
<?php endif; ?>

<!-- Add User Form -->
<div class="form-section">
    <h3 class="form-section-title">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <line x1="19" x2="19" y1="8" y2="14"/>
            <line x1="22" x2="16" y1="11" y2="11"/>
        </svg>
        Add New User
    </h3>
    <form method="POST" class="form-grid">
        <div class="form-group">
            <label class="form-label" for="name">Full Name</label>
            <input 
                type="text" 
                name="name" 
                id="name"
                class="form-control" 
                placeholder="Enter full name"
                required
            >
        </div>
        
        <div class="form-group">
            <label class="form-label" for="email">Email Address</label>
            <input 
                type="email" 
                name="email" 
                id="email"
                class="form-control" 
                placeholder="Enter email address"
                required
            >
        </div>
        
        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <input 
                type="password" 
                name="password" 
                id="password"
                class="form-control" 
                placeholder="Enter password"
                required
                minlength="6"
            >
        </div>
        
        <div class="form-group">
            <label class="form-label" for="role">Role</label>
            <select name="role" id="role" class="form-control" required>
                <option value="">Select a role</option>
                <option value="super_admin">Super Admin</option>
                <option value="ceo">CEO</option>
                <option value="supervisor">Supervisor</option>
                <option value="user">User</option>
            </select>
        </div>
        
        <div class="form-group" style="display: flex; align-items: flex-end;">
            <button type="submit" class="btn btn-primary w-full">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <line x1="19" x2="19" y1="8" y2="14"/>
                    <line x1="22" x2="16" y1="11" y2="11"/>
                </svg>
                Add User
            </button>
        </div>
    </form>
</div>

<!-- Users Table -->
<div class="table-container">
    <div class="table-header">
        <h3 class="table-title">All Users</h3>
        <span class="badge badge-neutral"><?= $totalUsers ?> total</span>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>User</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if($res->num_rows > 0): ?>
                <?php while($u = $res->fetch_assoc()): ?>
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div class="user-avatar" style="width: 36px; height: 36px; font-size: 0.875rem;">
                                <?= strtoupper(substr($u['name'], 0, 1)) ?>
                            </div>
                            <strong><?= htmlspecialchars($u['name']) ?></strong>
                        </div>
                    </td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td>
                        <?php 
                        $roleColors = [
                            'super_admin' => 'danger',
                            'ceo' => 'primary',
                            'supervisor' => 'success',
                            'user' => 'neutral'
                        ];
                        $roleColor = $roleColors[$u['role']] ?? 'neutral';
                        $roleLabel = ucwords(str_replace('_', ' ', $u['role']));
                        ?>
                        <span class="badge badge-<?= $roleColor ?>">
                            <?= $roleLabel ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-success">
                            <span class="status-dot success"></span>
                            Active
                        </span>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">
                        <div class="empty-state">
                            <svg class="empty-state-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                            <h4 class="empty-state-title">No users yet</h4>
                            <p class="empty-state-description">Start by adding your first user using the form above.</p>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'layout/footer.php'; ?>
