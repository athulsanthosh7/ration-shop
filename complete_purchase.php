<?php
session_start();
include 'db.php';

$total = 0;

/* GET CARD ID */
$card_id = $_POST['card_id'] ?? null;

if(!$card_id){
    die("Invalid access");
}

/* CHECK CART */
if(!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0){
    die("Cart is empty");
}

/* LOOP THROUGH CART */
foreach($_SESSION['cart'] as $item){

    $pid   = $item['pid'];
    $qty   = $item['qty'];
    $price = $item['price'];

    /* 1️⃣ INSERT INTO PURCHASE TABLE */
   $sql1 = "INSERT INTO system.purchase
(purchase_id, card_id, product_id, quantity, purchase_date)
VALUES (SYSTEM.PURCHASE_SEQ.NEXTVAL, :cid, :pid, :qty, SYSDATE)";

    $stid1 = oci_parse($conn, $sql1);

    oci_bind_by_name($stid1, ":cid", $card_id);
    oci_bind_by_name($stid1, ":pid", $pid);
    oci_bind_by_name($stid1, ":qty", $qty);

    $res1 = oci_execute($stid1, OCI_NO_AUTO_COMMIT);

    if(!$res1){
        $e = oci_error($stid1);
        oci_rollback($conn);
        die("Error inserting purchase: ".$e['message']);
    }

    /* 2️⃣ UPDATE STOCK */
    $sql2 = "UPDATE system.stock
             SET quantity = quantity - :qty
             WHERE product_id = :pid";

    $stid2 = oci_parse($conn, $sql2);

    oci_bind_by_name($stid2, ":qty", $qty);
    oci_bind_by_name($stid2, ":pid", $pid);

    $res2 = oci_execute($stid2, OCI_NO_AUTO_COMMIT);

    if(!$res2){
        $e = oci_error($stid2);
        oci_rollback($conn);
        die("Error updating stock: ".$e['message']);
    }

    /* CALCULATE TOTAL */
    $total += $qty * $price;
}

/* ✅ COMMIT TRANSACTION */
oci_commit($conn);

/* CLEAR CART */
$_SESSION['cart'] = [];
?>

<!DOCTYPE html>
<html>
<head>
<title>Success</title>
<link rel="stylesheet" href="style.css">
</head>

<body class="form-page">

<div class="success-box">

<div class="success-icon">✔</div>

<h2>Purchase Successful!</h2>

<p>Total Amount: ₹<?php echo $total; ?></p>

<a href="index.html" class="success-btn">Home</a>

</div>

<div class="toast">
Purchase completed successfully!
</div>

</body>
</html>