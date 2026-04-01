<?php
include 'db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

/* ===== INPUT ===== */
$id    = trim($_POST['id']);
$price = trim($_POST['price']);
$stock = trim($_POST['stock']);

/* ===== VALIDATION ===== */
if(!is_numeric($id)){
    die("Invalid ID");
}

if(!is_numeric($price) || $price <= 0){
    die("Invalid price");
}

if(!is_numeric($stock) || $stock < 0){
    die("Invalid stock");
}

/* CARD TYPE */
if(isset($_POST['card_type'])){
    $card = implode(", ", $_POST['card_type']);
} else {
    $card = "ALL";
}

/* ===== UPDATE PRODUCT ===== */
$sql1 = "UPDATE system.product
         SET price = :price,
             available_to = :card
         WHERE product_id = :id";

$stid1 = oci_parse($conn, $sql1);

oci_bind_by_name($stid1, ":price", $price);
oci_bind_by_name($stid1, ":card", $card);
oci_bind_by_name($stid1, ":id", $id);

if(!oci_execute($stid1)){
    $e = oci_error($stid1);
    die("Error updating product: " . $e['message']);
}

/* ===== UPDATE STOCK ===== */
$sql2 = "UPDATE system.stock
         SET quantity = :qty
         WHERE product_id = :id";

$stid2 = oci_parse($conn, $sql2);

oci_bind_by_name($stid2, ":qty", $stock);
oci_bind_by_name($stid2, ":id", $id);

if(!oci_execute($stid2)){
    $e = oci_error($stid2);
    die("Error updating stock: " . $e['message']);
}

oci_commit($conn);

header("Location: orderby.php");
exit;
?>
