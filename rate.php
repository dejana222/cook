<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($r = (int)($_POST['rating'] ?? 0)) && $r <= 5) {
    $pdo->prepare("
        INSERT INTO ratings (user_id, recipe_id, rating)
        VALUES (?, ?, ?) 
        ON DUPLICATE KEY UPDATE rating = VALUES(rating)
    ")->execute([$_SESSION['user_id'], (int)$_POST['recipe_id'], $r]);
}
header('Location: recipe_show.php?id=' . (int)($_POST['recipe_id'] ?? 0));
exit;
?>
