<?php
session_start();
require 'db.php';
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit;
}

// Add a new item
if (isset($_POST['add'])) {
    $name = $_POST['name'] ?? '';
    $date_purchased = $_POST['date_purchased'] ?: null;
    $purchased_price = $_POST['purchased_price'] ?: 0;
    $quantity_on_hand = $_POST['quantity_on_hand'] ?: 1;

    $imageData = null;
    $imageType = null;
    if (!empty($_FILES['image']['tmp_name'])) {
        $imageData = file_get_contents($_FILES['image']['tmp_name']);
        $imageType = $_FILES['image']['type'];
    }

    $stmt = $pdo->prepare("INSERT INTO disney_ears
        (name,date_purchased,purchased_price,quantity_on_hand,image,image_type)
        VALUES(:name,:date_purchased,:purchased_price,:quantity_on_hand,:image,:image_type)");

    $stmt->bindValue(':name', $name);
    $stmt->bindValue(':date_purchased', $date_purchased);
    $stmt->bindValue(':purchased_price', $purchased_price);
    $stmt->bindValue(':quantity_on_hand', $quantity_on_hand);
    $stmt->bindValue(':image', $imageData, PDO::PARAM_LOB);
    $stmt->bindValue(':image_type', $imageType);
    $stmt->execute();
    header('Location: admin_dashboard.php');
    exit;
}

// Record a sale
if (isset($_POST['sell'])) {
    $id = $_POST['id'];
    $sold_qty = $_POST['sold_quantity'] ?: 1;
    $selling_price = $_POST['selling_price'] ?: 0;
    $shipping_price = $_POST['shipping_price'] ?: 0;
    $date_sold = $_POST['date_sold'] ?: date('Y-m-d');

    // Decrease quantity
    $stmt = $pdo->prepare("UPDATE disney_ears
        SET quantity_on_hand = quantity_on_hand - :sold_qty,
            selling_price = :selling_price,
            shipping_price = :shipping_price,
            date_sold = :date_sold
        WHERE id=:id");
    $stmt->execute([
        ':sold_qty' => $sold_qty,
        ':selling_price' => $selling_price,
        ':shipping_price' => $shipping_price,
        ':date_sold' => $date_sold,
        ':id' => $id
    ]);
    header('Location: admin_dashboard.php');
    exit;
}

// Delete item
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM disney_ears WHERE id=?");
    $stmt->execute([$_GET['delete']]);
    header('Location: admin_dashboard.php');
    exit;
}

// Fetch all items
$stmt = $pdo->query("SELECT * FROM disney_ears ORDER BY name ASC");
$items = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>
<style>
body{font-family:Arial;margin:20px;background:#f5f5f5}
.container{max-width:1200px;margin:auto;background:#fff;padding:20px;border-radius:8px}
h1{text-align:center}
table{width:100%;border-collapse:collapse;margin-top:20px}
th,td{padding:8px;border-bottom:1px solid #ddd;text-align:left}
img{width:80px;height:60px;object-fit:cover;border-radius:4px}
form.inline{display:inline-block}
.btn{padding:6px 10px;background:#007bff;color:#fff;text-decoration:none;border-radius:4px;margin-right:4px}
.btn-danger{background:#d9534f}
.add-form input, .add-form input[type="number"], .add-form input[type="date"]{width:150px;margin-right:6px;margin-bottom:6px;padding:4px}
</style>
</head>
<body>
<div class="container">
<h1>Admin Dashboard</h1>

<h2>Add New Ear Set</h2>
<form class="add-form" method="POST" enctype="multipart/form-data">
<input type="text" name="name" placeholder="Name" required>
<input type="date" name="date_purchased">
<input type="number" step="0.01" name="purchased_price" placeholder="Purchase Price">
<input type="number" name="quantity_on_hand" placeholder="Quantity" value="1">
<input type="file" name="image" accept="image/*">
<button type="submit" name="add">Add Item</button>
</form>

<h2>Inventory</h2>
<table>
<tr>
<th>Image</th><th>Name</th><th>Purchased</th><th>Purchase Price</th><th>Sold</th><th>Sell Price</th><th>Shipping</th><th>Qty on Hand</th><th>Actions</th>
</tr>
<?php foreach ($items as $it): ?>
<tr>
<td><a href="admin_detail.php?id=<?= $it['id'] ?>"><img src="image.php?id=<?= $it['id'] ?>" alt="<?= htmlspecialchars($it['name']) ?>"></a></td>
<td><?= htmlspecialchars($it['name']) ?></td>
<td><?= $it['date_purchased'] ?></td>
<td><?= $it['purchased_price'] ?></td>
<td><?= $it['date_sold'] ?? '-' ?></td>
<td><?= $it['selling_price'] ?? '-' ?></td>
<td><?= $it['shipping_price'] ?? '-' ?></td>
<td><?= $it['quantity_on_hand'] ?></td>
<td>
<a href="admin_detail.php?id=<?= $it['id'] ?>" class="btn">Edit</a>
<a href="?delete=<?= $it['id'] ?>" class="btn btn-danger" onclick="return confirm('Delete this item?')">Delete</a>
<form method="POST" class="inline">
<input type="hidden" name="id" value="<?= $it['id'] ?>">
<input type="number" name="sold_quantity" placeholder="Qty" style="width:50px">
<input type="number" step="0.01" name="selling_price" placeholder="Sell $">
<input type="number" step="0.01" name="shipping_price" placeholder="Shipping $">
<input type="date" name="date_sold" placeholder="Date">
<button type="submit" name="sell">Sell</button>
</form>
</td>
</tr>
<?php endforeach; ?>
</table>
</div>
</body>
</html>
<?php
