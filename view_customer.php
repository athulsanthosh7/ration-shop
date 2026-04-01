<?php
session_start();
include 'db.php';
?>

<!DOCTYPE html>
<html>
<head>
<title>View Customers</title>
<link rel="stylesheet" href="style.css">
</head>

<body class="form-page">

<!-- HEADER -->
<div class="top-bar">
<a href="index.html" class="back-btn">←</a>
<h2>View Customers</h2>
</div>

<!-- ================= TOAST ================= -->
<?php if(isset($_SESSION['msg'])){ ?>
<div class="toast show">
<?php echo $_SESSION['msg']; ?>
</div>
<?php unset($_SESSION['msg']); } ?>

<div class="container">

<table>

<tr>
<th>ID</th>
<th>Name</th>
<th>Card Type</th>
<th>Phone</th>
<th>Address</th>
<th>Action</th>
</tr>

<?php

$sql = "SELECT c.customer_id, c.name, c.address, c.phone,
       r.card_type
FROM system.customer c
JOIN system.ration_card r
ON c.customer_id = r.customer_id
ORDER BY c.customer_id ASC";

$stid = oci_parse($conn, $sql);
oci_execute($stid);

while($row = oci_fetch_array($stid)) {

$id = $row['CUSTOMER_ID'];
$name = $row['NAME'];
$card = $row['CARD_TYPE'];
$phone = $row['PHONE'];
$address = $row['ADDRESS'];

echo "<tr>";

/* CLICKABLE CELLS FOR MODAL */
echo "<td onclick=\"openModal('$name','$card')\">$id</td>";
echo "<td onclick=\"openModal('$name','$card')\">$name</td>";

if($card == "BPL")
    echo "<td onclick=\"openModal('$name','$card')\"><span class='badge bpl'>BPL</span></td>";
else if($card == "APL")
    echo "<td onclick=\"openModal('$name','$card')\"><span class='badge apl'>APL</span></td>";
else
    echo "<td onclick=\"openModal('$name','$card')\"><span class='badge aay'>AAY</span></td>";

echo "<td onclick=\"openModal('$name','$card')\">$phone</td>";
echo "<td onclick=\"openModal('$name','$card')\">$address</td>";

/* DELETE BUTTON */
echo "<td>
<a href='delete_customer.php?id=$id' class='remove-btn'>
Delete
</a>
</td>";

echo "</tr>";
}
?>

</table>

</div>

<!-- ================= MODAL ================= -->
<div id="customerModal" class="modal">

<div class="modal-content">

<span class="close-btn" onclick="closeModal()">×</span>

<h2 id="modalName"></h2>
<p id="modalCard"></p>

<h3>AVAILABLE ITEMS & MONTHLY QUOTA</h3>

<table id="modalTable">
<tr>
<th>Item</th>
<th>Quantity</th>
<th>Price (₹)</th>
</tr>
</table>

</div>
</div>

<!-- ================= JS ================= -->
<script>

function openModal(name, card){

document.getElementById("customerModal").style.display = "flex";

document.getElementById("modalName").innerText = name;
document.getElementById("modalCard").innerHTML =
"<span class='badge "+card.toLowerCase()+"'>"+card+"</span>";

let table = document.getElementById("modalTable");

table.innerHTML = `
<tr>
<th>Item</th>
<th>Quantity</th>
<th>Price (₹)</th>
</tr>`;

/* ITEMS BASED ON CARD */
let items = [];

if(card === "APL"){
items = [
["Rice","10 kg","₹10/kg"],
["Wheat","7 kg","₹8/kg"]
];
}
else if(card === "BPL"){
items = [
["Rice","20 kg","₹3/kg"],
["Wheat","15 kg","₹2/kg"],
["Sugar","3 kg","₹13.5/kg"],
["Kerosene","5 ltr","₹15/ltr"],
["Dal","3 kg","₹20/kg"],
["Cooking Oil","2 ltr","₹25/ltr"]
];
}
else{
items = [
["Rice","35 kg","₹1/kg"],
["Wheat","20 kg","₹1/kg"]
];
}

/* ADD ROWS */
items.forEach(item => {
let row = `<tr>
<td>${item[0]}</td>
<td>${item[1]}</td>
<td>${item[2]}</td>
</tr>`;
table.innerHTML += row;
});

}

function closeModal(){
document.getElementById("customerModal").style.display = "none";
}

window.onclick = function(event) {
let modal = document.getElementById("customerModal");
if (event.target == modal) {
modal.style.display = "none";
}
}

</script>
<?php if(isset($_SESSION['toast'])) { ?>
<div id="toast" class="toast show">
<?php 
echo $_SESSION['toast']; 
unset($_SESSION['toast']);
?>
</div>
<?php } ?>

<script>
setTimeout(() => {
let toast = document.getElementById("toast");
if(toast){
toast.classList.remove("show");
}
}, 3000);
</script>
</body>
</html>