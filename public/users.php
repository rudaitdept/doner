<?php 
include '../config/db.php'; 
include 'layout/header.php';
if($_SESSION['user']['role'] != 'super_admin'){
    die("Access Denied");
}
if($_POST){
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users(name,email,password,role) VALUES(?,?,?,?)");
    $stmt->bind_param("ssss", $_POST['name'], $_POST['email'], $pass, $_POST['role']);
    $stmt->execute();
}

$res = $conn->query("SELECT * FROM users");
?>

<h4>User Management</h4>

<form method="POST" class="row g-2">
<input name="name" class="form-control col" placeholder="Name" required>
<input name="email" class="form-control col" placeholder="Email" required>
<input type="password" name="password" class="form-control col" placeholder="Password" required>

<select name="role" class="form-control col">
<option value="super_admin">Super Admin</option>
<option value="ceo">CEO</option>
<option value="supervisor">Supervisor</option>
<option value="user">User</option>
</select>

<button class="btn btn-primary col">Add User</button>
</form>

<table class="table mt-3">
<tr><th>Name</th><th>Email</th><th>Role</th></tr>

<?php while($u=$res->fetch_assoc()){ ?>
<tr>
<td><?=$u['name']?></td>
<td><?=$u['email']?></td>
<td><?=$u['role']?></td>
</tr>
<?php } ?>

</table>

<?php include 'layout/footer.php'; ?>