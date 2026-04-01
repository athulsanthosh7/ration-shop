<?php
include 'db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

/* VALIDATION */
if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
    die("Invalid request");
}

$id = $_GET['id'];

/* DELETE PURCHASE HISTORY (CHILD TABLE) */
$sql1 = "DELETE FROM system.purchase WHERE product_id = :id";
$stid1 = oci_parse($conn, $sql1);
oci_bind_by_name($stid1, ":id", $id);

if(!oci_execute($stid1)){
    $e = oci_error($stid1);
    die("Error deleting purchase history: " . $e['message']);
}

/* DELETE STOCK */
$sql2 = "DELETE FROM system.stock WHERE product_id = :id";
$stid2 = oci_parse($conn, $sql2);
oci_bind_by_name($stid2, ":id", $id);

if(!oci_execute($stid2)){
    $e = oci_error($stid2);
    die("Error deleting stock: " . $e['message']);
}

/* DELETE PRODUCT */
$sql3 = "DELETE FROM system.product WHERE product_id = :id";
$stid3 = oci_parse($conn, $sql3);
oci_bind_by_name($stid3, ":id", $id);

if(!oci_execute($stid3)){
    $e = oci_error($stid3);
    die("Error deleting product: " . $e['message']);
}

oci_commit($conn);

header("Location: orderby.php");
exit;
?>
