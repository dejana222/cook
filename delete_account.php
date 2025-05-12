<?php
require 'config.php';
session_start();

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $password = $_POST['delete_password'];

    // Dohvatanje trenutne lozinke korisnika
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!$user) {
        header('Location: profile.php?error=Greška pri dohvatanju korisnika');
        exit;
    }

    // Provera lozinke
    if (!password_verify($password, $user['password'])) {
        header('Location: profile.php?error=Neispravna lozinka');
        exit;
    }

    // Brisanje povezanih podataka (komentari, ocene, omiljeni recepti, itd.)
    $pdo->prepare("DELETE FROM comments WHERE user_id = ?")->execute([$user_id]);
    $pdo->prepare("DELETE FROM ratings WHERE user_id = ?")->execute([$user_id]);
    $pdo->prepare("DELETE FROM favorites WHERE user_id = ?")->execute([$user_id]);
    $pdo->prepare("DELETE FROM recipe_categories WHERE recipe_id IN (SELECT id FROM recipes WHERE user_id = ?)")->execute([$user_id]);
    $pdo->prepare("DELETE FROM recipes WHERE user_id = ?")->execute([$user_id]);
    $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$user_id]);

    // Uništavanje sesije
    session_destroy();

    header('Location: login.php?success=Nalog uspešno obrisan');
    exit;
}

header('Location: profile.php');
exit;
?>