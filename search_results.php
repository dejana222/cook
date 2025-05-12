<?php
require 'config.php'; // povezivanje na bazu

header('Content-Type: application/json');

$recipeName = isset($_POST['recipeName']) ? trim($_POST['recipeName']) : '';
$ingredients = isset($_POST['ingredients']) ? $_POST['ingredients'] : [];

$conditions = [];
$params = [];
$ingredient_conditions = [];

// Ako postoji naziv recepta
if (!empty($recipeName)) {
    $conditions[] = 'r.title LIKE :recipeName';
    $params[':recipeName'] = "%$recipeName%";
}

// Ako postoje sastojci
if (!empty($ingredients)) {
    // Dinamički brojač za recepte koji imaju određeni broj sastojaka
    $ingredient_count = count($ingredients);
    
    // Priprema uslova za sastojke
    foreach ($ingredients as $index => $ingredient) {
        $param_name = ':ingredient' . $index;
        $ingredient_conditions[] = "i.name LIKE $param_name";
        $params[$param_name] = "%$ingredient%";
    }

    // Kreiranje podupita za brojanje podudaranja sastojaka
    $subquery = "SELECT ri.recipe_id
                 FROM recipe_ingredient ri
                 JOIN ingredients i ON ri.ingredient_id = i.id
                 WHERE " . implode(' OR ', $ingredient_conditions) . "
                 GROUP BY ri.recipe_id
                 HAVING COUNT(DISTINCT i.name) = :ingredient_count";
    
    $conditions[] = "r.id IN ($subquery)";
    $params[':ingredient_count'] = $ingredient_count;
}

// Ako nema uslova, vrati prazan rezultat
if (empty($conditions)) {
    echo json_encode([]);
    exit;
}

// Pravljenje glavnog upita
$query = "SELECT r.*, COALESCE(AVG(rt.rating), 0) AS avg_rating, COUNT(rt.id) AS rating_count
          FROM recipes r
          LEFT JOIN ratings rt ON r.id = rt.recipe_id
          WHERE " . implode(' AND ', $conditions) . "
          GROUP BY r.id
          ORDER BY avg_rating DESC";

// Priprema i izvršenje upita
$stmt = $pdo->prepare($query);
$stmt->execute($params);

$recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Vrati kao JSON
echo json_encode($recipes);
?>