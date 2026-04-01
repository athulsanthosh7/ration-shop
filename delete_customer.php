<?php
session_start();
include 'db.php';

if(isset($_POST['confirm_delete'])){

$id = $_POST['id'];

/* ================= DELETE PURCHASE ================= */
$sql1 = "DELETE FROM system.purchase 
WHERE card_id IN (
    SELECT card_id FROM system.ration_card WHERE customer_id = :id
)";
$stid1 = oci_parse($conn,$sql1);
oci_bind_by_name($stid1,":id",$id);
oci_execute($stid1);

/* ================= DELETE RATION CARD ================= */
$sql2 = "DELETE FROM system.ration_card 
WHERE customer_id = :id";
$stid2 = oci_parse($conn,$sql2);
oci_bind_by_name($stid2,":id",$id);
oci_execute($stid2);

/* ================= DELETE CUSTOMER ================= */
$sql3 = "DELETE FROM system.customer 
WHERE customer_id = :id";
$stid3 = oci_parse($conn,$sql3);
oci_bind_by_name($stid3,":id",$id);
oci_execute($stid3);

oci_commit($conn);

$_SESSION['msg'] = "Customer deleted successfully!";
header("Location: view_customer.php");
exit();
}

if(!isset($_GET['id'])){
header("Location: delete_customer.html");
exit();
}

$id = $_GET['id'];

$sql = "SELECT c.name, c.phone, r.card_type
FROM system.customer c
JOIN system.ration_card r
ON c.customer_id = r.customer_id
WHERE c.customer_id = :id";

$stid = oci_parse($conn,$sql);
oci_bind_by_name($stid,":id",$id);
oci_execute($stid);

$data = oci_fetch_array($stid);

if(!$data){
echo "<script>alert('Customer not found'); window.location='delete_customer.html';</script>";
exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Delete Customer</title>
<link rel="stylesheet" href="style.css">
</head>

<body class="form-page">

<div class="top-bar">
<a href="delete_customer.html" class="back-btn">←</a>
<h2>Delete Customer</h2>
</div>

<!-- SEARCH AGAIN -->
<div class="form-container">
<form method="GET" class="search-form">
<input type="number" name="id" value="<?php echo $id; ?>" required>
<button class="add-btn">Search</button>
</form>
</div>

<!-- WARNING CARD -->
<div class="delete-card">

<div class="delete-icon">⚠</div>

<div class="delete-content">

<h3><?php echo $data['NAME']; ?></h3>
<p>Card: <?php echo $data['CARD_TYPE']; ?> • Phone: <?php echo $data['PHONE']; ?></p>

<p class="warning-text">This action cannot be undone.</p>

<form method="POST">
<input type="hidden" name="id" value="<?php echo $id; ?>">

<button class="delete-confirm-btn" name="confirm_delete">
Confirm Delete
</button>

<a href="delete_customer.html" class="cancel-btn">Cancel</a>

</form>

</div>

</div>

</body>
</html>