<?php
session_start();
require 'db.php';
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit;
}

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM disney_ears WHERE id=?");
$stmt->execute([$id]);
$item = $stmt->fetch();
if (!$item) {
    header('Location: admin_dashboard.php');
    exit;
}

// Update item
if (isset($_POST['save'])) {
    $name = $_POST['name'];
    $date_purchased = $_POST['date_purchased'] ?: null;
    $purchased_price = $_POST['purchased_price'] ?: 0;
    $quantity_on_hand = $_POST['quantity_on_hand'] ?: 0;

    $replace_image = !empty($_FILES['image']['tmp_name']);
    if ($replace_image) {
        $imageData = file_get_contents($_FILES['image']['tmp_name']);
        $imageType = $_FILES['image']['type'];
        $sql = "UPDATE disney_ears SET name=:name,date_purchased=:date_purchased,purchased_price=:purchased_price,
        quantity_on_hand=:quantity_on_hand,image=:image,image_type=:image_type WHERE id=:id";
        $stmt2 = $pdo->prepare($sql);
        $stmt2->bindValue(':image', $imageData, PDO::PARAM_LOB);
        $stmt2->bindValue(':image_type', $imageType);
    } else {
        $sql = "UPDATE disney_ears SET name=:name,date_purchased=:date_purchased,purchased_price=:purchased_price,
        quantity_on_hand=:quantity_on_hand WHERE id=:id";
        $stmt2 = $pdo->prepare($sql);
    }

    $stmt2->execute([
        ':name' => $name,
        ':date_purchased' => $date_purchased,
        ':purchased_price' => $purchased_price,
        ':quantity_on_hand' => $quantity_on_hand,
        ':id' => $id
    ]);

    header("Location: admin_dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Disney Ear Set</title>
<style>
body{font-family:Arial;margin:20px;background:#f5f5f5}
.container{max-width:600px;margin:auto;background:#fff;padding:20px;border-radius:8px}
input,textarea{width:100%;padding:8px;margin:4px 0;border:1px solid #ccc;border-radius:4px}
img{max-width:200px;border-radius:6px}
button{padding:8px 12px;background:#007bff;color:#fff;border:none;border-radius:4px;cursor:pointer}
</style>
</head>
<body>
<div class="container">
<h2>Edit Disney Ear Set</h2>
<form method="POST" enctype="multipart/form-data">
<label>Name</label>
<input name="name" value="<?= htmlspecialchars($item['name']) ?>" required>

<label>Date Purchased</label>
<input type="date" name="date_purchased" value="<?= $item['date_purchased'] ?>">

<label>Purchased Price</label>
<input type="number" step="0.01" name="purchased_price" value="<?= $item['purchased_price'] ?>">

<label>Quantity on Hand</label>
<input type="number" name="quantity_on_hand" value="<?= $item['quantity_on_hand'] ?>">

<label>Current Image</label><br>
<?php if ($item['image']): ?>
<img src="image.php?id=<?= $item['id'] ?>" alt="image">
<?php else: ?>
<div style="width:180px;height:120px;background:#efefef;display:flex;align-items:center;justify-content:center;color:#888;border-radius:6px">No image</div>
<?php endif; ?>

<label>Replace Image</label>
<input type="file" name="image" accept="image/*">

<button type="submit" name="save">Save Changes</button>
<a href="admin_dashboard.php" style="margin-left:10px;">Cancel</a>
</form>
</div>
</body>
</html>
<?php
