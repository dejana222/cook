<?php
require 'config.php';
require 'helpers.php';


if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$recipe_id = (int)$_GET['id'];

// Proveri da li recept postoji i dohvati user_id
$stmt = $pdo->prepare("SELECT user_id FROM recipes WHERE id = ?");
$stmt->execute([$recipe_id]);
$recipe = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$recipe) {
    header('Location: index.php');
    exit;
}

// Proveri da li je trenutni korisnik autor recepta
$current_user_id = $_SESSION['user_id'];
if ($recipe['user_id'] !== $current_user_id) {
    header('Location: index.php');
    exit;
}

// Obriši recept i povezane podatke
try {
    // Obriši povezane sastojke iz recipe_ingredient
    $stmt = $pdo->prepare("DELETE FROM recipe_ingredient WHERE recipe_id = ?");
    $stmt->execute([$recipe_id]);

    // Obriši ocene (ratings) povezane sa receptom
    $stmt = $pdo->prepare("DELETE FROM ratings WHERE recipe_id = ?");
    $stmt->execute([$recipe_id]);

    // Obriši recept
    $stmt = $pdo->prepare("DELETE FROM recipes WHERE id = ?");
    $stmt->execute([$recipe_id]);

    // Preusmeri na početnu stranicu sa porukom o uspehu
    header('Location: index.php?message=Recept+je+uspešno+obrisan');
    exit;
} catch (PDOException $e) {
    header('Location: index.php?error=Greška+pri+brisanju+recepta');
    exit;
}
?>