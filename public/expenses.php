<?php 
include '../config/db.php'; 
include 'layout/header.php';

if(!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$success = false;
$error = '';

// Handle form submission
if($_POST){
    if(empty($_POST['name']) || empty($_POST['amount']) || empty($_POST['purpose']) || empty($_POST['date'])) {
        $error = 'All fields are required';
    } else {
        $stmt = $conn->prepare("INSERT INTO expenses(beneficiary_name, amount, purpose, date, added_by) VALUES(?, ?, ?, ?, ?)");
        $stmt->bind_param("sdssi", $_POST['name'], $_POST['amount'], $_POST['purpose'], $_POST['date'], $_SESSION['user']['id']);
        if($stmt->execute()) {
            $success = true;
        } else {
            $error = 'Failed to add expense';
        }
    }
}

// Handle approval
if(isset($_GET['approve'])){
    $id = intval($_GET['approve']);
    $conn->query("UPDATE expenses SET status='approved' WHERE id=" . $id);
    header("Location: expenses.php?approved=1");
    exit;
}

// Get all expenses
$res = $conn->query("SELECT e.*, u.name as added_by_name FROM expenses e LEFT JOIN users u ON e.added_by = u.id ORDER BY e.id DESC");

// Get stats
$totalExpenses = $conn->query("SELECT COUNT(*) c FROM expenses")->fetch_assoc()['c'];
$approvedExpenses = $conn->query("SELECT COUNT(*) c FROM expenses WHERE status='approved'")->fetch_assoc()['c'];
$pendingExpenses = $conn->query("SELECT COUNT(*) c FROM expenses WHERE status='pending'")->fetch_assoc()['c'];
?>

<div class="page-header">
    <h1 class="page-title">Expenses</h1>
    <p class="page-description">Track and manage outgoing expenses</p>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon primary">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect width="20" height="14" x="2" y="5" rx="2"/>
                <line x1="2" x2="22" y1="10" y2="10"/>
            </svg>
        </div>
        <div class="stat-content">
            <p class="stat-label">Total Expenses</p>
            <p class="stat-value"><?= $totalExpenses ?></p>
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
            <p class="stat-value"><?= $approvedExpenses ?></p>
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
            <p class="stat-value"><?= $pendingExpenses ?></p>
        </div>
    </div>
</div>

<?php if($success): ?>
<div class="alert alert-success">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
        <polyline points="22 4 12 14.01 9 11.01"/>
    </svg>
    Expense added successfully!
</div>
<?php endif; ?>

<?php if(isset($_GET['approved'])): ?>
<div class="alert alert-success">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
        <polyline points="22 4 12 14.01 9 11.01"/>
    </svg>
    Expense approved successfully!
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

<!-- Add Expense Form -->
<div class="form-section">
    <h3 class="form-section-title">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" x2="12" y1="8" y2="16"/>
            <line x1="8" x2="16" y1="12" y2="12"/>
        </svg>
        Add New Expense
    </h3>
    <form method="POST" class="form-grid">
        <div class="form-group">
            <label class="form-label" for="name">Beneficiary Name</label>
            <input 
                type="text" 
                name="name" 
                id="name"
                class="form-control" 
                placeholder="Enter beneficiary name"
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
            <label class="form-label" for="purpose">Purpose</label>
            <input 
                type="text" 
                name="purpose" 
                id="purpose"
                class="form-control" 
                placeholder="e.g., Office Supplies, Travel, Equipment"
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
            <button type="submit" class="btn btn-warning w-full">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" x2="12" y1="8" y2="16"/>
                    <line x1="8" x2="16" y1="12" y2="12"/>
                </svg>
                Add Expense
            </button>
        </div>
    </form>
</div>

<!-- Expenses Table -->
<div class="table-container">
    <div class="table-header">
        <h3 class="table-title">All Expenses</h3>
        <span class="badge badge-neutral"><?= $totalExpenses ?> total</span>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>Beneficiary</th>
                <th>Amount</th>
                <th>Purpose</th>
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
                        <strong><?= htmlspecialchars($r['beneficiary_name']) ?></strong>
                    </td>
                    <td>
                        <span class="amount amount-negative"><?= number_format($r['amount'], 2) ?></span>
                    </td>
                    <td>
                        <span class="tag"><?= htmlspecialchars($r['purpose']) ?></span>
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
                            <a href="?approve=<?= $r['id'] ?>" class="btn btn-success btn-sm" onclick="return confirm('Approve this expense?')">
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
                                <line x1="12" x2="12" y1="2" y2="22"/>
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                            </svg>
                            <h4 class="empty-state-title">No expenses yet</h4>
                            <p class="empty-state-description">Start by adding your first expense using the form above.</p>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'layout/footer.php'; ?>
