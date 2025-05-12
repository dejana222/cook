<?php
header('Content-Type: application/json');
require 'config.php';

// Dohvati parametre
$name = isset($_GET['name']) ? trim($_GET['name']) : '';
$ingredients = isset($_GET['ingredients']) ? array_filter(explode(',', trim($_GET['ingredients']))) : [];

try {
    // Osnovni upit
    $sql = "SELECT r.*, COALESCE(AVG(rt.rating), 0) AS avg_rating, COUNT(rt.id) AS rating_count
            FROM recipes r
            LEFT JOIN ratings rt ON r.id = rt.recipe_id";

    // Ako su sastojci prosleđeni, pridruži tabele za sastojke
    if (!empty($ingredients)) {
        $sql .= " JOIN recipe_ingredient ri ON r.id = ri.recipe_id
                  JOIN ingredients i ON ri.ingredient_id = i.id";
    }

    // Dodaj uslove
    $conditions = [];
    $params = [];

    // Filtriranje po nazivu recepta
    if ($name) {
        $conditions[] = "r.title LIKE ?";
        $params[] = "%$name%";
    }

    // Filtriranje po sastojcima
    if (!empty($ingredients)) {
        $placeholders = implode(',', array_fill(0, count($ingredients), '?'));
        $conditions[] = "i.name IN ($placeholders)";
        $params = array_merge($params, $ingredients);
    }

    // Ako nema uslova (ni naziv ni sastojci), vrati prazan niz
    if (empty($conditions)) {
        echo json_encode([]);
        exit;
    }

    // Dodaj uslove u upit
    $sql .= " WHERE " . implode(' AND ', $conditions);

    // Grupisanje po receptu
    $sql .= " GROUP BY r.id";

    // Izvrši upit
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($recipes);
} catch (PDOException $e) {
    // Log the error if needed
    echo json_encode([]);
}
?>