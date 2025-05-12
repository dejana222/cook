<?php
require 'config.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recipe_id = (int)$_POST['recipe_id'];
    $content = htmlspecialchars(trim($_POST['content']));

    if ($content) {
        $stmt = $pdo->prepare("INSERT INTO comments (user_id, recipe_id, content) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $recipe_id, $content]);
    }

    header('Location: recipe_show.php?id=' . $recipe_id);
    exit;
}
?>