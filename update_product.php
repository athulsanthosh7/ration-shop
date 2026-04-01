<?php
include 'db.php';

$id    = $_POST['id'];
$price = $_POST['price'];
$stock = $_POST['stock'];

if(isset($_POST['card_type'])){
    $card = implode(", ", $_POST['card_type']);
} else {
    $card = "ALL";
}

/* UPDATE PRODUCT */
$sql1 = "UPDATE system.product
         SET price = :price,
             available_to = :card
         WHERE product_id = :id";

$stid1 = oci_parse($conn, $sql1);

oci_bind_by_name($stid1, ":price", $price);
oci_bind_by_name($stid1, ":card", $card);
oci_bind_by_name($stid1, ":id", $id);

oci_execute($stid1);

/* UPDATE STOCK */
$sql2 = "UPDATE system.stock
         SET quantity = :qty
         WHERE product_id = :id";

$stid2 = oci_parse($conn, $sql2);

oci_bind_by_name($stid2, ":qty", $stock);
oci_bind_by_name($stid2, ":id", $id);

oci_execute($stid2);

oci_commit($conn);

header("Location: orderby.php");
exit;
?>