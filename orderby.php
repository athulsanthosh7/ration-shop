<?php
include 'db.php';

/* FETCH PRODUCT + STOCK + CARD TYPE */
$sql = "SELECT p.product_id,
               p.product_name,
               p.price,
               p.available_to,
               s.quantity
        FROM system.product p
        LEFT JOIN system.stock s
        ON p.product_id = s.product_id
        ORDER BY p.price ASC";

$stid = oci_parse($conn, $sql);
oci_execute($stid);
?>

<!DOCTYPE html>
<html>
<head>
<title>Product List</title>
<link rel="stylesheet" href="style.css">
</head>

<body class="form-page">

<div class="top-bar">
    <a href="index.html" class="back-btn">&#8592;</a>
    <h2>Product List</h2>
</div>

<div class="list-header">
    <p>📦 Products ordered by price (ascending)</p>
    <a href="#" class="add-btn" onclick="openAddModal()">+ Add Product</a>
</div>

<div class="table-container">

<table>
<tr>
<th>ID</th>
<th>Product</th>
<th>Available To</th>
<th>Price (₹)</th>
<th>Stock</th>
<th>Actions</th>
</tr>

<?php
while ($row = oci_fetch_array($stid)) {

    $product = $row['PRODUCT_NAME'];
    $availability = $row['AVAILABLE_TO'];

    if(!$availability || trim($availability) == ""){
        $availability = "All";
    }

    echo "<tr>";

    echo "<td>".$row['PRODUCT_ID']."</td>";
    echo "<td>".$product."</td>";
    echo "<td><span class='badge'>".$availability."</span></td>";
    echo "<td>₹".$row['PRICE']."</td>";

    if($product == "Kerosene"){
        echo "<td>".$row['QUANTITY']." ltr</td>";
    } else {
        echo "<td>".$row['QUANTITY']." kg</td>";
    }

    echo "<td>

    <button class='edit-btn' onclick=\"openEditModal(
    '".$row['PRODUCT_ID']."',
    '".$product."',
    '".$row['PRICE']."',
    '".$row['QUANTITY']."',
    '".$availability."'
    )\">✏️</button>

    <a href='delete_product.php?id=".$row['PRODUCT_ID']."' 
       class='remove-btn'
       onclick=\"return confirm('Delete this product?')\">🗑️</a>

    </td>";

    echo "</tr>";
}
?>

</table>

</div>

<!-- ADD MODAL -->
<div id="addModal" class="modal">
  <div class="modal-content">

    <span class="close" onclick="closeAddModal()">&times;</span>

    <h3>Add New Product</h3>

    <form action="add_product.php" method="POST">

      <label>Product Name</label>
      <input type="text" name="name" required>

      <div class="row">
        <div>
          <label>Price (₹)</label>
          <input type="number" name="price" required>
        </div>

        <div>
          <label>Stock</label>
          <input type="number" name="stock" required>
        </div>
      </div>

      <label>Available For</label>
      <div class="checkbox-group">
        <label><input type="checkbox" name="card_type[]" value="APL"> APL</label>
        <label><input type="checkbox" name="card_type[]" value="BPL"> BPL</label>
        <label><input type="checkbox" name="card_type[]" value="AAY"> AAY</label>
        <label><input type="checkbox" name="card_type[]" value="ALL"> All</label>
      </div>

      <div class="modal-actions">
        <button type="button" onclick="closeAddModal()" class="cancel-btn">Cancel</button>
        <button type="submit" name="add_product">Add Product</button>
      </div>

    </form>

  </div>
</div>

<!-- EDIT MODAL -->
<div id="editModal" class="modal">
  <div class="modal-content">

    <span class="close" onclick="closeEditModal()">&times;</span>

    <h3 id="editTitle">Update Product</h3>

    <form action="update_product.php" method="POST">

      <input type="hidden" name="id" id="editId">

      <label>Price (₹)</label>
      <input type="number" name="price" id="editPrice" required>

      <label>Stock</label>
      <input type="number" name="stock" id="editStock" required>

      <label>Available For</label>
      <div class="checkbox-group">
        <label><input type="checkbox" name="card_type[]" value="APL" id="editAPL"> APL</label>
        <label><input type="checkbox" name="card_type[]" value="BPL" id="editBPL"> BPL</label>
        <label><input type="checkbox" name="card_type[]" value="AAY" id="editAAY"> AAY</label>
        <label><input type="checkbox" name="card_type[]" value="ALL" id="editALL"> All</label>
      </div>

      <div class="modal-actions">
        <button type="button" onclick="closeEditModal()" class="cancel-btn">Cancel</button>
        <button type="submit">Update</button>
      </div>

    </form>

  </div>
</div>

<script>

function openAddModal() {
  document.getElementById("addModal").style.display = "flex";
}

function closeAddModal() {
  document.getElementById("addModal").style.display = "none";
}

function openEditModal(id, name, price, stock, available) {

  document.getElementById("editModal").style.display = "flex";

  document.getElementById("editId").value = id;
  document.getElementById("editPrice").value = price;
  document.getElementById("editStock").value = stock;
  document.getElementById("editTitle").innerText = "Update " + name;

  document.querySelectorAll('#editModal input[type=checkbox]').forEach(cb => cb.checked = false);

  if(available){
    let arr = available.split(",");
    arr.forEach(val => {
      val = val.trim();
      if(val === "APL") document.getElementById("editAPL").checked = true;
      if(val === "BPL") document.getElementById("editBPL").checked = true;
      if(val === "AAY") document.getElementById("editAAY").checked = true;
      if(val === "ALL") document.getElementById("editALL").checked = true;
    });
  }
}

function closeEditModal() {
  document.getElementById("editModal").style.display = "none";
}

</script>

</body>
</html>