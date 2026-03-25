<?php 
include '../config/db.php'; 
include 'layout/header.php';

if(!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

if($_SESSION['user']['role'] == 'ceo'){
    echo '<div class="alert alert-danger">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" x2="12" y1="8" y2="12"/>
            <line x1="12" x2="12.01" y1="16" y2="16"/>
        </svg>
        Access Denied: CEO cannot manage donations
    </div>';
    include 'layout/footer.php';
    exit;
}

$success = false;
$error = '';

// Handle form submission
if($_POST){
    if(empty($_POST['name']) || empty($_POST['amount']) || empty($_POST['source']) || empty($_POST['date'])) {
        $error = 'All fields are required';
    } else {
        $stmt = $conn->prepare("INSERT INTO donations(donor_name, amount, source, date, added_by) VALUES(?, ?, ?, ?, ?)");
        $stmt->bind_param("sdssi", $_POST['name'], $_POST['amount'], $_POST['source'], $_POST['date'], $_SESSION['user']['id']);
        if($stmt->execute()) {
            $success = true;
        } else {
            $error = 'Failed to add donation';
        }
    }
}

// Handle approval
if(isset($_GET['approve'])){
    $id = intval($_GET['approve']);
    $conn->query("UPDATE donations SET status='approved' WHERE id=" . $id);
    header("Location: donations.php?approved=1");
    exit;
}

// Get all donations
$res = $conn->query("SELECT d.*, u.name as added_by_name FROM donations d LEFT JOIN users u ON d.added_by = u.id ORDER BY d.id DESC");

// Get stats
$totalDonations = $conn->query("SELECT COUNT(*) c FROM donations")->fetch_assoc()['c'];
$approvedDonations = $conn->query("SELECT COUNT(*) c FROM donations WHERE status='approved'")->fetch_assoc()['c'];
$pendingDonations = $conn->query("SELECT COUNT(*) c FROM donations WHERE status='pending'")->fetch_assoc()['c'];
?>

<div class="page-header">
    <h1 class="page-title">Donations</h1>
    <p class="page-description">Track and manage incoming donations</p>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon primary">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
            </svg>
        </div>
        <div class="stat-content">
            <p class="stat-label">Total Donations</p>
            <p class="stat-value"><?= $totalDonations ?></p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon success">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                <polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
        </div>
        <div class="stat-content">
            <p class="stat-label">Approved</p>
            <p class="stat-value"><?= $approvedDonations ?></p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon warning">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <polyline points="12 6 12 12 16 14"/>
            </svg>
        </div>
        <div class="stat-content">
            <p class="stat-label">Pending Approval</p>
            <p class="stat-value"><?= $pendingDonations ?></p>
        </div>
    </div>
</div>

<?php if($success): ?>
<div class="alert alert-success">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
        <polyline points="22 4 12 14.01 9 11.01"/>
    </svg>
    Donation added successfully!
</div>
<?php endif; ?>

<?php if(isset($_GET['approved'])): ?>
<div class="alert alert-success">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
        <polyline points="22 4 12 14.01 9 11.01"/>
    </svg>
    Donation approved successfully!
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

<!-- Add Donation Form -->
<div class="form-section">
    <h3 class="form-section-title">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" x2="12" y1="8" y2="16"/>
            <line x1="8" x2="16" y1="12" y2="12"/>
        </svg>
        Add New Donation
    </h3>
    <form method="POST" class="form-grid">
        <div class="form-group">
            <label class="form-label" for="name">Donor Name</label>
            <input 
                type="text" 
                name="name" 
                id="name"
                class="form-control" 
                placeholder="Enter donor name"
                required
            >
        </div>
        
        <div class="form-group">
            <label class="form-label" for="amount">Amount</label>
            <input 
                type="number" 
                name="amount" 
                id="amount"
                class="form-control" 
                placeholder="0.00"
                step="0.01"
                min="0"
                required
            >
        </div>
        
        <div class="form-group">
            <label class="form-label" for="source">Source</label>
            <input 
                type="text" 
                name="source" 
                id="source"
                class="form-control" 
                placeholder="e.g., Online, Cash, Bank Transfer"
                required
            >
        </div>
        
        <div class="form-group">
            <label class="form-label" for="date">Date</label>
            <input 
                type="date" 
                name="date" 
                id="date"
                class="form-control"
                value="<?= date('Y-m-d') ?>"
                required
            >
        </div>
        
        <div class="form-group" style="display: flex; align-items: flex-end;">
            <button type="submit" class="btn btn-success w-full">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" x2="12" y1="8" y2="16"/>
                    <line x1="8" x2="16" y1="12" y2="12"/>
                </svg>
                Add Donation
            </button>
        </div>
    </form>
</div>

<!-- Donations Table -->
<div class="table-container">
    <div class="table-header">
        <h3 class="table-title">All Donations</h3>
        <span class="badge badge-neutral"><?= $totalDonations ?> total</span>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>Donor Name</th>
                <th>Amount</th>
                <th>Source</th>
                <th>Date</th>
                <th>Added By</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if($res->num_rows > 0): ?>
                <?php while($r = $res->fetch_assoc()): ?>
                <tr>
                    <td>
                        <strong><?= htmlspecialchars($r['donor_name']) ?></strong>
                    </td>
                    <td>
                        <span class="amount amount-positive"><?= number_format($r['amount'], 2) ?></span>
                    </td>
                    <td>
                        <span class="tag"><?= htmlspecialchars($r['source']) ?></span>
                    </td>
                    <td>
                        <span class="date"><?= date('M d, Y', strtotime($r['date'])) ?></span>
                    </td>
                    <td>
                        <?= htmlspecialchars($r['added_by_name'] ?? 'Unknown') ?>
                    </td>
                    <td>
                        <?php if($r['status'] == 'approved'): ?>
                            <span class="badge badge-success">
                                <span class="status-dot success"></span>
                                Approved
                            </span>
                        <?php else: ?>
                            <span class="badge badge-warning">
                                <span class="status-dot warning"></span>
                                Pending
                            </span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($r['status'] != 'approved'): ?>
                            <a href="?approve=<?= $r['id'] ?>" class="btn btn-success btn-sm" onclick="return confirm('Approve this donation?')">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                                Approve
                            </a>
                        <?php else: ?>
                            <span class="text-muted" style="font-size: 0.75rem;">Completed</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <svg class="empty-state-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>
                            </svg>
                            <h4 class="empty-state-title">No donations yet</h4>
                            <p class="empty-state-description">Start by adding your first donation using the form above.</p>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'layout/footer.php'; ?>
