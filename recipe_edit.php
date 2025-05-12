<?php
require 'header.php';
require 'helpers.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$recipe_id = (int)$_GET['id'];

// Dohvatanje recepta
$stmt = $pdo->prepare("SELECT * FROM recipes WHERE id = ?");
$stmt->execute([$recipe_id]);
$recipe = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$recipe) {
    header('Location: index.php');
    exit;
}

// Provera da li je korisnik autor
if (!isLoggedIn() || $recipe['user_id'] != $_SESSION['user_id']) {
    header('Location: index.php');
    exit;
}

// Dohvatanje sastojaka
$stmt = $pdo->prepare("SELECT i.name, ri.quantity, ri.ingredient_id 
                       FROM recipe_ingredient ri 
                       JOIN ingredients i ON ri.ingredient_id = i.id 
                       WHERE ri.recipe_id = ?");
$stmt->execute([$recipe_id]);
$ingredients = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obrada forme za ažuriranje
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $steps = trim($_POST['steps']);
    $ingredients = $_POST['ingredients'] ?? [];
    $quantities = $_POST['quantities'] ?? [];

    // Validacija (po potrebi dodajte više provera)
    if (empty($title) || empty($description) || empty($steps)) {
        $error = "Sva polja su obavezna.";
    } else {
        // Ažuriranje recepta
        $stmt = $pdo->prepare("UPDATE recipes SET title = ?, description = ?, steps = ? WHERE id = ?");
        $stmt->execute([$title, $description, $steps, $recipe_id]);

        // Brisanje starih sastojaka
        $stmt = $pdo->prepare("DELETE FROM recipe_ingredient WHERE recipe_id = ?");
        $stmt->execute([$recipe_id]);

        // Dodavanje novih sastojaka
        foreach ($ingredients as $index => $ingredient_name) {
            if (!empty($ingredient_name) && !empty($quantities[$index])) {
                // Provera da li sastojak već postoji
                $stmt = $pdo->prepare("SELECT id FROM ingredients WHERE name = ?");
                $stmt->execute([$ingredient_name]);
                $ingredient = $stmt->fetch();

                if ($ingredient) {
                    $ingredient_id = $ingredient['id'];
                } else {
                    // Kreiranje novog sastojka
                    $stmt = $pdo->prepare("INSERT INTO ingredients (name) VALUES (?)");
                    $stmt->execute([$ingredient_name]);
                    $ingredient_id = $pdo->lastInsertId();
                }

                // Dodavanje u recipe_ingredient
                $stmt = $pdo->prepare("INSERT INTO recipe_ingredient (recipe_id, ingredient_id, quantity) VALUES (?, ?, ?)");
                $stmt->execute([$recipe_id, $ingredient_id, $quantities[$index]]);
            }
        }

        // Obrada slike (ako je uploadovana)
        if (!empty($_FILES['image']['name'])) {
            $image = $_FILES['image'];
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($image["name"]);
            if (move_uploaded_file($image["tmp_name"], $target_file)) {
                $stmt = $pdo->prepare("UPDATE recipes SET image = ? WHERE id = ?");
                $stmt->execute([basename($image["name"]), $recipe_id]);
            }
        }

        header("Location: recipe_show.php?id=$recipe_id");
        exit;
    }
}
?>

<section class="container">
    <h2>Uredi recept</h2>
    
    <?php if (isset($error)): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form action="recipe_edit.php?id=<?php echo $recipe_id; ?>" method="POST" enctype="multipart/form-data">
        <label for="title">Naslov</label>
        <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($recipe['title']); ?>" required>

        <label for="description">Opis</label>
        <textarea name="description" id="description" required><?php echo htmlspecialchars($recipe['description']); ?></textarea>

        <label for="steps">Uputstva</label>
        <textarea name="steps" id="steps" required><?php echo htmlspecialchars($recipe['steps']); ?></textarea>

        <h3>Sastojci</h3>
        <div id="ingredients">
            <?php foreach ($ingredients as $index => $ingredient): ?>
                <div class="ingredient-row">
                    <input type="text" name="ingredients[]" value="<?php echo htmlspecialchars($ingredient['name']); ?>" placeholder="Sastojak" required>
                    <input type="text" name="quantities[]" value="<?php echo htmlspecialchars($ingredient['quantity']); ?>" placeholder="Količina" required>
                    <button type="button" class="remove-ingredient">Ukloni</button>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" id="add-ingredient">Dodaj sastojak</button>

        <label for="image">Slika</label>
        <input type="file" name="image" id="image" accept="image/*">

        <button type="submit">Sačuvaj promene</button>
    </form>
</section>

<script>
    // Dodavanje novog polja za sastojak
    document.getElementById('add-ingredient').addEventListener('click', function() {
        const container = document.getElementById('ingredients');
        const row = document.createElement('div');
        row.className = 'ingredient-row';
        row.innerHTML = `
            <input type="text" name="ingredients[]" placeholder="Sastojak" required>
            <input type="text" name="quantities[]" placeholder="Količina" required>
            <button type="button" class="remove-ingredient">Ukloni</button>
        `;
        container.appendChild(row);
    });

    // Uklanjanje polja za sastojak
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-ingredient')) {
            e.target.parentElement.remove();
        }
    });
</script>

<?php require 'footer.php'; ?>