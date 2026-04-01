<?php
include 'db.php';

/* ================= GROUP BY QUERY ================= */
$sql = "SELECT p.product_name,
               SUM(pr.quantity) AS total_qty,
               SUM(pr.quantity * p.price) AS total_amount
        FROM system.purchase pr
        JOIN system.product p
        ON pr.product_id = p.product_id
        GROUP BY p.product_name
        ORDER BY p.product_name";

$stid = oci_parse($conn, $sql);

if (!oci_execute($stid)) {
    $e = oci_error($stid);
    echo "Error: ".$e['message'];
}

/* STORE DATA */
$products = [];
$qtys = [];
$amounts = [];

while ($row = oci_fetch_array($stid)) {
    $products[] = $row['PRODUCT_NAME'];
    $qtys[] = $row['TOTAL_QTY'];
    $amounts[] = $row['TOTAL_AMOUNT'];
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Purchase Report</title>
<link rel="stylesheet" href="style.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="form-page">

<!-- HEADER -->
<div class="top-bar">
<a href="index.html" class="back-btn">&#8592;</a>
<h2>Purchase Report</h2>
</div>

<!-- ================= CHART ================= -->
<div class="report-card">
<h3>Revenue by Product</h3>
<canvas id="myChart"></canvas>
</div>

<!-- ================= GROUP TABLE ================= -->
<div class="table-container">

<table>
<tr>
<th>Product</th>
<th>Total Qty</th>
<th>Total Amount (₹)</th>
</tr>

<?php
for ($i = 0; $i < count($products); $i++) {
    echo "<tr>";
    echo "<td>".$products[$i]."</td>";
    echo "<td>".$qtys[$i]."</td>";
    echo "<td>₹".number_format($amounts[$i])."</td>";
    echo "</tr>";
}
?>

</table>
</div>

<!-- ================= PURCHASE HISTORY ================= -->
<div class="report-box">

<h3>📜 Purchase History</h3>

<table>
<tr>
<th>Customer</th>
<th>Card</th>
<th>Item</th>
<th>Qty</th>
<th>Amount</th>
<th>Date</th>
</tr>

<?php
$sql2 = "SELECT 
c.name,
r.card_type,
p.product_name,
pr.quantity,
(p.price * pr.quantity) AS amount,
TO_CHAR(pr.purchase_date,'DD-MON-YYYY') AS pdate

FROM system.purchase pr
JOIN system.product p ON pr.product_id = p.product_id
JOIN system.ration_card r ON pr.card_id = r.card_id
JOIN system.customer c ON r.customer_id = c.customer_id

ORDER BY pr.purchase_date DESC";

$stid2 = oci_parse($conn, $sql2);
oci_execute($stid2);

while($row = oci_fetch_array($stid2)){

echo "<tr>";
echo "<td>".$row['NAME']."</td>";
echo "<td><span class='card-badge ".$row['CARD_TYPE']."'>".$row['CARD_TYPE']."</span></td>";
echo "<td>".$row['PRODUCT_NAME']."</td>";
echo "<td>".$row['QUANTITY']."</td>";
echo "<td>₹".$row['AMOUNT']."</td>";
echo "<td>".$row['PDATE']."</td>";
echo "</tr>";

}
?>

</table>

</div>

<!-- ================= CHART JS ================= -->
<script>
const ctx = document.getElementById('myChart');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($products); ?>,
        datasets: [{
            label: 'Total Amount',
            data: <?php echo json_encode($amounts); ?>,
            backgroundColor: '#d45a0c',
            borderRadius: 8
        }]
    },
    options: {
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return " ₹ " + context.raw;
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>

</body>
</html>