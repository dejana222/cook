<?php
// Privremeno uključi prikaz grešaka za debagovanje
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'config.php';
require 'helpers.php';

header('Content-Type: application/json');

$response = ['success' => false, 'error' => 'Nepoznata greška'];

try {
    // Provera da li je korisnik prijavljen
    if (!isLoggedIn()) {
        throw new Exception('Morate biti prijavljeni da biste poslali poruku.');
    }

    // Provera da li je zahtev POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Nevažeći zahtev.');
    }

    // Provera sesije
    $user_id = $_SESSION['user_id'] ?? null;
    if (!$user_id) {
        throw new Exception('Korisnički ID nije dostupan. Sesija možda nije ispravno započeta.');
    }

    // Provera poruke
    $message = trim($_POST['message'] ?? '');
    if (empty($message)) {
        throw new Exception('Poruka ne može biti prazna.');
    }

    // Provera konekcije sa bazom
    if (!isset($pdo)) {
        throw new Exception('Konekcija sa bazom nije uspostavljena. Proverite config.php.');
    }

    // Upis poruke u bazu
    $stmt = $pdo->prepare("INSERT INTO chat_messages (user_id, message) VALUES (?, ?)");
    $stmt->execute([$user_id, $message]);

    $response['success'] = true;
    $response['error'] = null;
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response);
exit;
?>