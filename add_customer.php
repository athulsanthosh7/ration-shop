<?php

include 'db.php';

$id = $_POST['id'];
$name = $_POST['name'];
$address = $_POST['address'];
$phone = $_POST['phone'];
$card_type = $_POST['card_type'];

/* Insert into CUSTOMER */

$sql1 = "INSERT INTO system.customer 
VALUES (:id,:name,:address,:phone)";

$stid1 = oci_parse($conn,$sql1);

oci_bind_by_name($stid1,":id",$id);
oci_bind_by_name($stid1,":name",$name);
oci_bind_by_name($stid1,":address",$address);
oci_bind_by_name($stid1,":phone",$phone);

oci_execute($stid1);

/* Generate Card ID */
$card_id = $id + 100;

/* Insert into RATION_CARD */

$sql2 = "INSERT INTO system.ration_card 
VALUES (:card_id,:card_type,:customer_id)";

$stid2 = oci_parse($conn,$sql2);

oci_bind_by_name($stid2,":card_id",$card_id);
oci_bind_by_name($stid2,":card_type",$card_type);
oci_bind_by_name($stid2,":customer_id",$id);

oci_execute($stid2);

/* Redirect to view */

header("Location: view_customer.php");
exit();

?>