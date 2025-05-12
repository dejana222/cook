<?php
error_reporting(0);
ini_set('display_errors', 0);

require 'config.php';
require 'helpers.php';

header('Content-Type: application/json');

try {
    $stmt = $pdo->prepare("SELECT cm.*, u.name 
                           FROM chat_messages cm 
                           LEFT JOIN users u ON cm.user_id = u.id 
                           ORDER BY cm.created_at DESC 
                           LIMIT 50");
    $stmt->execute();
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $messages = array_reverse($messages);

    foreach ($messages as &$msg) {
        $msg['created_at'] = date('H:i', strtotime($msg['created_at']));
        $msg['message'] = htmlspecialchars($msg['message']);
        $msg['name'] = htmlspecialchars($msg['name'] ?? 'Nepoznati korisnik');
    }

    echo json_encode($messages);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Greška pri dohvatanju poruka: ' . $e->getMessage()]);
}

exit;
?>