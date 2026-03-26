<?php include '../config/db.php'; include 'layout/header.php';
if($_SESSION['user']['role'] == 'ceo'){
    die("CEO cannot add donations");
}
if($_POST){
$stmt=$conn->prepare("INSERT INTO donations(donor_name,amount,source,date,added_by) VALUES(?,?,?,?,?)");
$stmt->bind_param("sdssi",$_POST['name'],$_POST['amount'],$_POST['source'],$_POST['date'],$_SESSION['user']['id']);
$stmt->execute();
}
if(isset($_GET['approve'])){
$conn->query("UPDATE donations SET status='approved' WHERE id=".$_GET['approve']);
}
$res=$conn->query("SELECT * FROM donations ORDER BY id DESC");
?>
<h4>Donations</h4>
<form method="POST" class="row g-2">
<input name="name" class="form-control col" placeholder="Name">
<input name="amount" class="form-control col">
<input name="source" class="form-control col">
<input type="date" name="date" class="form-control col">
<button class="btn btn-success col">Add</button>
</form>

<table class="table mt-3">
<tr><th>Name</th><th>Amount</th><th>Status</th><th>Action</th></tr>
<?php while($r=$res->fetch_assoc()){ ?>
<tr>
<td><?=$r['donor_name']?></td>
<td><?=$r['amount']?></td>
<td><?=$r['status']?></td>
<td><a href="?approve=<?=$r['id']?>" class="btn btn-sm btn-success">Approve</a></td>
</tr>
<?php } ?>
</table>
<?php include 'layout/footer.php'; ?>
