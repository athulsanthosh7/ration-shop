<?php
include 'db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$pid = $_POST['pid'];
$qty = $_POST['qty'];
$cid = $_POST['cid'];

/* CHECK CURRENT STOCK */
$sql = "SELECT quantity FROM system.stock WHERE product_id = :pid";
$stid = oci_parse($conn, $sql);
oci_bind_by_name($stid, ":pid", $pid);
oci_execute($stid);

$row = oci_fetch_array($stid);
$current = $row['QUANTITY'];

/* VALIDATION */
if($qty <= 0) {
    echo "Invalid quantity!";
    exit;
}

if($qty > $current) {
    echo "Not enough stock!";
    exit;
}

/* UPDATE STOCK */
$sql = "UPDATE system.stock
        SET quantity = quantity - :qty
        WHERE product_id = :pid";

$stid = oci_parse($conn, $sql);
oci_bind_by_name($stid, ":qty", $qty);
oci_bind_by_name($stid, ":pid", $pid);

if(!oci_execute($stid)) {
    $e = oci_error($stid);
    echo "ERROR: ".$e['message'];
    exit;
}

oci_commit($conn);

/* REDIRECT BACK */
header("Location: purchase.php?cid=".$cid);
exit;
?>