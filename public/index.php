<?php 
include '../config/db.php'; 
include 'layout/header.php';

if(!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Get totals
$donations = $conn->query("SELECT SUM(amount) t FROM donations WHERE status='approved'")->fetch_assoc()['t'] ?: 0;
$expenses = $conn->query("SELECT SUM(amount) t FROM expenses WHERE status='approved'")->fetch_assoc()['t'] ?: 0;
$balance = $donations - $expenses;

// Get pending counts
$pendingDonations = $conn->query("SELECT COUNT(*) c FROM donations WHERE status='pending'")->fetch_assoc()['c'] ?: 0;
$pendingExpenses = $conn->query("SELECT COUNT(*) c FROM expenses WHERE status='pending'")->fetch_assoc()['c'] ?: 0;

// Get recent donations
$recentDonations = $conn->query("SELECT * FROM donations ORDER BY id DESC LIMIT 5");

// Get recent expenses
$recentExpenses = $conn->query("SELECT * FROM expenses ORDER BY id DESC LIMIT 5");
?>

<div class="page-header">
    <h1 class="page-title">Dashboard</h1>
    <p class="page-description">Overview of your donation management system</p>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card animate-fade-in" style="animation-delay: 0.1s">
        <div class="stat-icon success">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>
            </svg>
        </div>
        <div class="stat-content">
            <p class="stat-label">Total Donations</p>
            <p class="stat-value amount amount-positive"><?= number_format($donations, 2) ?></p>
            <?php if($pendingDonations > 0): ?>
            <span class="stat-change" style="background: var(--accent-warning-muted); color: var(--accent-warning);">
                <?= $pendingDonations ?> pending approval
            </span>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="stat-card animate-fade-in" style="animation-delay: 0.2s">
        <div class="stat-icon danger">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" x2="12" y1="2" y2="22"/>
                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
            </svg>
        </div>
        <div class="stat-content">
            <p class="stat-label">Total Expenses</p>
            <p class="stat-value amount amount-negative"><?= number_format($expenses, 2) ?></p>
            <?php if($pendingExpenses > 0): ?>
            <span class="stat-change" style="background: var(--accent-warning-muted); color: var(--accent-warning);">
                <?= $pendingExpenses ?> pending approval
            </span>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="stat-card animate-fade-in" style="animation-delay: 0.3s">
        <div class="stat-icon primary">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
            </svg>
        </div>
        <div class="stat-content">
            <p class="stat-label">Current Balance</p>
            <p class="stat-value amount <?= $balance >= 0 ? 'amount-positive' : 'amount-negative' ?>"><?= number_format($balance, 2) ?></p>
            <span class="stat-change <?= $balance >= 0 ? 'positive' : 'negative' ?>">
                <?= $balance >= 0 ? 'Surplus' : 'Deficit' ?>
            </span>
        </div>
    </div>
</div>

<!-- Chart Section -->
<div class="chart-container animate-fade-in" style="animation-delay: 0.4s; margin-bottom: var(--spacing-xl);">
    <div class="chart-header">
        <h3 class="chart-title">Financial Overview</h3>
        <div class="chart-legend">
            <div class="chart-legend-item">
                <span class="chart-legend-dot" style="background: var(--accent-success);"></span>
                Donations
            </div>
            <div class="chart-legend-item">
                <span class="chart-legend-dot" style="background: var(--accent-danger);"></span>
                Expenses
            </div>
        </div>
    </div>
    <div class="chart-wrapper">
        <canvas id="chart"></canvas>
    </div>
</div>

<!-- Quick Actions -->
<div class="form-section animate-fade-in" style="animation-delay: 0.5s">
    <h3 class="form-section-title">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/>
            <polyline points="13 2 13 9 20 9"/>
        </svg>
        Quick Actions
    </h3>
    <div class="quick-actions">
        <?php if($_SESSION['user']['role'] != 'ceo'): ?>
        <a href="donations.php" class="btn btn-success quick-action-btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>
            </svg>
            Manage Donations
        </a>
        <?php endif; ?>
        <a href="expenses.php" class="btn btn-warning quick-action-btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" x2="12" y1="2" y2="22"/>
                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
            </svg>
            Manage Expenses
        </a>
        <?php if($_SESSION['user']['role'] == 'super_admin'): ?>
        <a href="users.php" class="btn btn-primary quick-action-btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
            Manage Users
        </a>
        <?php endif; ?>
    </div>
</div>

<!-- Recent Activity Tables -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: var(--spacing-xl);">
    <!-- Recent Donations -->
    <div class="table-container animate-fade-in" style="animation-delay: 0.6s">
        <div class="table-header">
            <h3 class="table-title">Recent Donations</h3>
            <a href="donations.php" class="btn btn-ghost btn-sm">View All</a>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>Donor</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if($recentDonations->num_rows > 0): ?>
                    <?php while($r = $recentDonations->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($r['donor_name']) ?></td>
                        <td class="amount amount-positive"><?= number_format($r['amount'], 2) ?></td>
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
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="table-empty">No donations yet</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Recent Expenses -->
    <div class="table-container animate-fade-in" style="animation-delay: 0.7s">
        <div class="table-header">
            <h3 class="table-title">Recent Expenses</h3>
            <a href="expenses.php" class="btn btn-ghost btn-sm">View All</a>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>Beneficiary</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if($recentExpenses->num_rows > 0): ?>
                    <?php while($r = $recentExpenses->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($r['beneficiary_name']) ?></td>
                        <td class="amount amount-negative"><?= number_format($r['amount'], 2) ?></td>
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
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="table-empty">No expenses yet</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
// Chart.js configuration
const ctx = document.getElementById('chart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Donations', 'Expenses'],
        datasets: [{
            label: 'Amount',
            data: [<?= $donations ?>, <?= $expenses ?>],
            backgroundColor: [
                'rgba(34, 197, 94, 0.8)',
                'rgba(239, 68, 68, 0.8)'
            ],
            borderColor: [
                'rgba(34, 197, 94, 1)',
                'rgba(239, 68, 68, 1)'
            ],
            borderWidth: 1,
            borderRadius: 8,
            barThickness: 60
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: '#18181b',
                titleColor: '#fafafa',
                bodyColor: '#a1a1aa',
                borderColor: '#27272a',
                borderWidth: 1,
                cornerRadius: 8,
                padding: 12,
                callbacks: {
                    label: function(context) {
                        return 'Amount: ' + context.parsed.y.toLocaleString('en-US', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                    }
                }
            }
        },
        scales: {
            x: {
                grid: {
                    display: false
                },
                ticks: {
                    color: '#71717a',
                    font: {
                        family: 'Inter',
                        size: 12
                    }
                }
            },
            y: {
                beginAtZero: true,
                grid: {
                    color: '#27272a',
                    drawBorder: false
                },
                ticks: {
                    color: '#71717a',
                    font: {
                        family: 'Inter',
                        size: 12
                    },
                    callback: function(value) {
                        return value.toLocaleString();
                    }
                }
            }
        }
    }
});
</script>

<?php include 'layout/footer.php'; ?>
