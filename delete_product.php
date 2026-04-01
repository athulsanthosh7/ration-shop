<?php
include 'db.php';

$id = $_GET['id'];

oci_execute(oci_parse($conn,
"DELETE FROM system.stock WHERE product_id = $id"));

oci_execute(oci_parse($conn,
"DELETE FROM system.product WHERE product_id = $id"));

oci_commit($conn);

header("Location: orderby.php");
exit;
?>