<?php
include 'db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if(isset($_POST['add_product'])){

    $name  = $_POST['name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    /* HANDLE CARD TYPES */
    if(isset($_POST['card_type'])){
        $card_types = implode(", ", $_POST['card_type']); // APL, BPL
    } else {
        $card_types = "ALL";
    }

    /* INSERT PRODUCT */
    $sql1 = "INSERT INTO system.product (product_id, product_name, price, available_to)
             VALUES (system.product_seq.NEXTVAL, :name, :price, :card)
             RETURNING product_id INTO :new_id";

    $stid1 = oci_parse($conn, $sql1);

    oci_bind_by_name($stid1, ":name", $name);
    oci_bind_by_name($stid1, ":price", $price);
    oci_bind_by_name($stid1, ":card", $card_types);
    oci_bind_by_name($stid1, ":new_id", $new_id, 32);

    if (!oci_execute($stid1)) {
        $e = oci_error($stid1);
        echo "ERROR INSERT PRODUCT: " . $e['message'];
        exit;
    }

    /* INSERT STOCK */
    $sql2 = "INSERT INTO system.stock (stock_id, product_id, quantity)
             VALUES (system.stock_seq.NEXTVAL, :pid, :qty)";

    $stid2 = oci_parse($conn, $sql2);

    oci_bind_by_name($stid2, ":pid", $new_id);
    oci_bind_by_name($stid2, ":qty", $stock);

    if (!oci_execute($stid2)) {
        $e = oci_error($stid2);
        echo "ERROR INSERT STOCK: " . $e['message'];
        exit;
    }

    /* COMMIT */
    oci_commit($conn);

    /* REDIRECT (NO ALERT) */
    header("Location: orderby.php");
    exit;
}
?>