<?php
require 'config.php';
require 'helpers.php';

// Privremeno uključivanje prikaza grešaka za debagovanje
error_reporting(E_ALL);
ini_set('display_errors', 1);

redirectIfNotLoggedIn();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recipe_id'])) {
    $recipe_id = (int)$_POST['recipe_id'];
    $user_id = $_SESSION['user_id'];

    // Proveravamo da li je recept već u omiljenima
    $stmt = $pdo->prepare("SELECT id FROM favorites WHERE user_id = ? AND recipe_id = ?");
    $stmt->execute([$user_id, $recipe_id]);
    $favorite = $stmt->fetch();

    if ($favorite) {
        // Ako je recept već u omiljenima, uklanjamo ga
        $stmt = $pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND recipe_id = ?");
        $success = $stmt->execute([$user_id, $recipe_id]);
        if (!$success) {
            // Ako upit ne uspe, vraćamo grešku
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Greška prilikom uklanjanja iz omiljenih: ' . implode(', ', $stmt->errorInfo())]);
            exit;
        }
        $is_favorite = false;
    } else {
        // Ako nije u omiljenima, dodajemo ga
        $stmt = $pdo->prepare("INSERT INTO favorites (user_id, recipe_id) VALUES (?, ?)");
        $success = $stmt->execute([$user_id, $recipe_id]);
        if (!$success) {
            // Ako upit ne uspe, vraćamo grešku
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Greška prilikom dodavanja u omiljene: ' . implode(', ', $stmt->errorInfo())]);
            exit;
        }
        $is_favorite = true;
    }

    // Vraćamo status (da li je recept sada omiljen ili ne) kao JSON
    header('Content-Type: application/json');
    echo json_encode(['is_favorite' => $is_favorite]);
    exit;
}

header('Location: index.php');
exit;
?>