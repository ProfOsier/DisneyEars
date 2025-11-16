<?php
require 'db.php';
$view = $_GET['view'] ?? 'list';

$stmt = $pdo->query("SELECT id, name FROM disney_ears ORDER BY name ASC");
$items = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Disney Ear Collection</title>
<style>
body{font-family:Arial;margin:20px;background:#f5f5f5}
.container{max-width:1000px;margin:auto;background:#fff;padding:20px;border-radius:8px}
h1{text-align:center}
.top-right{position:absolute;top:20px;right:20px}
a.btn{background:#007bff;color:#fff;padding:6px 10px;text-decoration:none;border-radius:4px}
.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:16px;margin-top:20px}
.card{text-align:center;padding:10px;background:#fafafa;border-radius:8px;transition:transform 0.2s}
.card:hover{transform:scale(1.05)}
.card img{width:100%;height:120px;object-fit:cover;border-radius:6px}
</style>
</head>
<body>
<div class="top-right"><a href="admin_login.php" class="btn">Admin Login</a></div>
<div class="container">
<h1>Disney Ear Collection</h1>

<div style="margin-top:10px">
<a href="?view=list" class="btn">List View</a>
<a href="?view=gallery" class="btn">Gallery View</a>
</div>

<?php if ($view === 'list'): ?>
<table style="width:100%;margin-top:20px;border-collapse:collapse">
<tr><th>Image</th><th>Name</th></tr>
<?php foreach ($items as $i): ?>
<tr>
<td><img src="image.php?id=<?= $i['id'] ?>" alt="<?= htmlspecialchars($i['name']) ?>"></td>
<td><?= htmlspecialchars($i['name']) ?></td>
</tr>
<?php endforeach; ?>
</table>
<?php else: ?>
<div class="grid">
<?php foreach ($items as $i): ?>
<div class="card">
<a href="image.php?id=<?= $i['id'] ?>" target="_blank">
<img src="image.php?id=<?= $i['id'] ?>" alt="<?= htmlspecialchars($i['name']) ?>">
<p><?= htmlspecialchars($i['name']) ?></p>
</a>
</div>
<?php endforeach; ?>
</div>
<?php endif; ?>
</div>
</body>
</html>
