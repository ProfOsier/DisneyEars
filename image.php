<?php
require 'db.php';
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT image, image_type FROM disney_ears WHERE id=?");
$stmt->execute([$id]);
$row = $stmt->fetch();

if (!$row || !$row['image']) {
    http_response_code(404);
    exit('No image found');
}

header("Content-Type: " . $row['image_type']);
echo $row['image'];
?>
<?php
