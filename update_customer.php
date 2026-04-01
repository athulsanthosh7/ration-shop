<?php
session_start();
include 'db.php';

$id = isset($_GET['id']) ? $_GET['id'] : "";

$name = "";
$phone = "";
$address = "";
$card_type = "";

/* FETCH DATA */
if ($id != "") {

    $sql = "SELECT c.name, c.phone, c.address, r.card_type
            FROM system.customer c
            LEFT JOIN system.ration_card r
            ON c.customer_id = r.customer_id
            WHERE c.customer_id = :id";

    $stid = oci_parse($conn, $sql);
    oci_bind_by_name($stid, ":id", $id);
    oci_execute($stid);

    if ($row = oci_fetch_array($stid)) {
        $name = $row['NAME'];
        $phone = $row['PHONE'];
        $address = $row['ADDRESS'];
        $card_type = $row['CARD_TYPE'];
    } else {
        echo "<script>alert('Customer not found'); window.location.href='update_customer.html';</script>";
    }
}

/* UPDATE */
if (isset($_POST['update'])) {

    $id = $_POST['id'];
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $card_type = $_POST['card_type'];

    /* UPDATE CUSTOMER */
    $sql1 = "UPDATE system.customer 
             SET name = :name, phone = :phone, address = :address
             WHERE customer_id = :id";

    $stid1 = oci_parse($conn, $sql1);

    oci_bind_by_name($stid1, ":name", $name);
    oci_bind_by_name($stid1, ":phone", $phone);
    oci_bind_by_name($stid1, ":address", $address);
    oci_bind_by_name($stid1, ":id", $id);

    oci_execute($stid1);

    /* UPDATE CARD */
    $sql2 = "UPDATE system.ration_card 
             SET card_type = :card_type
             WHERE customer_id = :id";

    $stid2 = oci_parse($conn, $sql2);

    oci_bind_by_name($stid2, ":card_type", $card_type);
    oci_bind_by_name($stid2, ":id", $id);

    oci_execute($stid2);

    oci_commit($conn);

    $_SESSION['toast'] = "Customer Updated Successfully!";
header("Location: view_customer.php");
exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Update Customer</title>
<link rel="stylesheet" href="style.css">
</head>

<body class="form-page">

<!-- HEADER -->
<div class="top-bar">
<a href="update_customer.html" class="back-btn">&#8592;</a>
<h2>Update Customer</h2>
</div>

<!-- FORM -->
<div class="form-container">

<form method="POST">

<input type="hidden" name="id" value="<?php echo $id; ?>">

<label>Full Name</label>
<input type="text" name="name" value="<?php echo $name; ?>" required>

<label>Phone Number</label>
<input type="text" name="phone" value="<?php echo $phone; ?>" required>

<label>Address</label>
<input type="text" name="address" value="<?php echo $address; ?>" required>

<label>Card Type</label>
<select name="card_type" required>
<option value="">Select Card Type</option>
<option value="APL" <?php if($card_type=="APL") echo "selected"; ?>>APL</option>
<option value="BPL" <?php if($card_type=="BPL") echo "selected"; ?>>BPL</option>
<option value="AAY" <?php if($card_type=="AAY") echo "selected"; ?>>AAY</option>
</select>

<button type="submit" name="update">Update Customer</button>

</form>

</div>

<!-- ================= TOAST ================= -->
<?php if(isset($_SESSION['toast'])) { ?>
<div id="toast" class="toast show">
<?php 
echo $_SESSION['toast']; 
unset($_SESSION['toast']);
?>
</div>
<?php } ?>

<script>
setTimeout(() => {
let toast = document.getElementById("toast");
if(toast){
toast.classList.remove("show");
}
}, 3000);
</script>

</body>
</html>