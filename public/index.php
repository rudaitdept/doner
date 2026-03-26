<?php include '../config/db.php'; include 'layout/header.php';
if(!isset($_SESSION['user'])) header("Location:login.php");
$d=$conn->query("SELECT SUM(amount) t FROM donations WHERE status='approved'")->fetch_assoc()['t'];
$e=$conn->query("SELECT SUM(amount) t FROM expenses WHERE status='approved'")->fetch_assoc()['t'];
?>
<div class="row">
<div class="col-md-4"><div class="card p-3 bg-success text-white">Donations: <?=$d?:0?></div></div>
<div class="col-md-4"><div class="card p-3 bg-danger text-white">Expenses: <?=$e?:0?></div></div>
<div class="col-md-4"><div class="card p-3 bg-primary text-white">Balance: <?=($d-$e)?></div></div>
</div>

<canvas id="chart"></canvas>
<script>
new Chart(document.getElementById('chart'),{
 type:'bar',
 data:{labels:['Donations','Expenses'],datasets:[{data:[<?=$d?:0?>,<?=$e?:0?>]}]}
});
</script>

<hr>
<a href="donations.php" class="btn btn-primary">Donations</a>
<a href="expenses.php" class="btn btn-warning">Expenses</a>
<a href="users.php" class="btn btn-dark">Manage Users</a>
<?php include 'layout/footer.php'; ?>
