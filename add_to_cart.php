<?php
session_start();

if(!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

/* REMOVE ITEM */
if(isset($_GET['remove'])) {

    $cid = $_GET['cid']; // GET CID

    unset($_SESSION['cart'][$_GET['remove']]);
    $_SESSION['cart'] = array_values($_SESSION['cart']);

    header("Location: purchase.php?cid=".$cid);
    exit;
}

/* ADD ITEM */
$item = [
    "pid"=>$_POST['pid'],
    "name"=>$_POST['pname'],
    "price"=>$_POST['price'],
    "qty"=>$_POST['qty']
];

$_SESSION['cart'][] = $item;

header("Location: purchase.php?cid=".$_POST['cid']);
exit;
?>