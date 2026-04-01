<?php
session_start();
include 'db.php';

if(!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Purchase</title>
<link rel="stylesheet" href="style.css">
</head>

<body class="form-page">

<div class="top-bar">
<a href="index.html" class="back-btn">←</a>
<h2>Purchase</h2>
</div>

<!-- ================= CUSTOMER ================= -->
<div class="purchase-section">

<form method="GET">
<select name="cid" onchange="this.form.submit()" required>
<option value="">Select Customer</option>

<?php
$sql = "SELECT c.customer_id, c.name, r.card_type
FROM system.customer c
JOIN system.ration_card r
ON c.customer_id = r.customer_id";

$stid = oci_parse($conn, $sql);
oci_execute($stid);

while($row = oci_fetch_array($stid)) {
$selected = (isset($_GET['cid']) && $_GET['cid']==$row['CUSTOMER_ID'])?"selected":"";
echo "<option value='".$row['CUSTOMER_ID']."' $selected>";
echo $row['NAME']." (".$row['CARD_TYPE'].")";
echo "</option>";
}
?>

</select>
</form>

</div>

<?php
if(isset($_GET['cid'])) {

$cid = $_GET['cid'];

/* GET CARD DETAILS */
$sql = "SELECT card_id, card_type FROM system.ration_card WHERE customer_id=:cid";
$stid = oci_parse($conn,$sql);
oci_bind_by_name($stid,":cid",$cid);
oci_execute($stid);
$row = oci_fetch_array($stid);

$card = $row['CARD_TYPE'];
$card_id = $row['CARD_ID'];

/* MONTHLY LIMITS */
$limits = [
    "Rice"=>10,
    "Wheat"=>7,
    "Sugar"=>2,
    "Kerosene"=>3,
    "Dal"=>1,
    "Cooking Oil"=>1
];

/* FILTER */
if($card=="APL") $condition="p.product_name IN ('Rice','Wheat')";
elseif($card=="BPL") $condition="p.product_name IN ('Rice','Wheat','Sugar','Kerosene')";
else $condition="1=1";
?>

<!-- ================= PRODUCTS ================= -->
<div class="purchase-table">

<h3>🛒 Available Items</h3>

<table>
<tr>
<th>Item</th>
<th>Price</th>
<th>Remaining</th>
<th>Stock</th>
<th>Qty</th>
<th>Action</th>
</tr>

<?php
$sql="SELECT 
p.product_id,
p.product_name,
p.price,
s.quantity,

NVL((
SELECT SUM(pr.quantity)
FROM system.purchase pr
WHERE pr.product_id = p.product_id
AND pr.card_id = :card_id
AND TO_CHAR(pr.purchase_date,'MMYYYY') = TO_CHAR(SYSDATE,'MMYYYY')
),0) AS purchased

FROM system.product p
JOIN system.stock s ON p.product_id=s.product_id
WHERE $condition";

$stid=oci_parse($conn,$sql);
oci_bind_by_name($stid,":card_id",$card_id);
oci_execute($stid);

while($row=oci_fetch_array($stid)) {

$name = $row['PRODUCT_NAME'];
$monthly = $limits[$name] ?? 5;
$purchased = $row['PURCHASED'];

$remaining = $monthly - $purchased;
if($remaining < 0) $remaining = 0;

$stock = $row['QUANTITY'];
$allowed = min($remaining, $stock);

echo "<tr>";

echo "<td>$name</td>";
echo "<td>₹".$row['PRICE']."</td>";
echo "<td>$remaining</td>";

/* ===== STOCK DISPLAY ===== */
if($stock == 0){
    echo "<td><span class='out-stock'>Out of Stock</span></td>";
} else {
    echo "<td>$stock</td>";
}

echo "<td>";

/* ===== LOGIC ===== */
if($stock == 0){

    // OUT OF STOCK
    echo "<input type='number' disabled placeholder='0'>";
    echo "</td>";
    echo "<td><button class='out-btn' disabled>Out of Stock</button></td>";

}
elseif($allowed > 0){

    // NORMAL
    echo "<form method='POST' action='add_to_cart.php' style='display:flex;gap:5px;'>";

    echo "<input type='number' name='qty' min='1' max='$allowed' required>";

    echo "<input type='hidden' name='pid' value='".$row['PRODUCT_ID']."'>";
    echo "<input type='hidden' name='pname' value='$name'>";
    echo "<input type='hidden' name='price' value='".$row['PRICE']."'>";
    echo "<input type='hidden' name='cid' value='$cid'>";

    echo "</td>";
    echo "<td><button class='add-btn-small'>Add</button></td>";

    echo "</form>";

}
else{

    // LIMIT REACHED
    echo "<input type='number' disabled placeholder='0'>";
    echo "</td>";
    echo "<td><button class='disabled-btn' disabled>Limit Reached</button></td>";

}

echo "</tr>";
}
?>

</table>
</div>

<!-- ================= CART ================= -->
<div class="cart-box">

<h3>🛒 Cart</h3>

<table>
<tr>
<th>Item</th>
<th>Qty</th>
<th>Rate</th>
<th>Amount</th>
<th>Action</th>
</tr>

<?php
$total = 0;

foreach($_SESSION['cart'] as $index=>$item){

$amt = $item['qty'] * $item['price'];
$total += $amt;

echo "<tr>";
echo "<td>".$item['name']."</td>";
echo "<td>".$item['qty']."</td>";
echo "<td>₹".$item['price']."</td>";
echo "<td>₹".$amt."</td>";

echo "<td>
<a href='add_to_cart.php?remove=$index&cid=$cid' class='remove-btn'>Remove</a>
</td>";

echo "</tr>";
}

echo "<tr><td colspan='3'><b>Total</b></td><td colspan='2'><b>₹$total</b></td></tr>";
?>

</table>

<form method="POST" action="complete_purchase.php">
<input type="hidden" name="card_id" value="<?php echo $card_id; ?>">
<button class="purchase-btn"> Complete Purchase</button>
</form>

</div>

<?php } ?>

</body>
</html>