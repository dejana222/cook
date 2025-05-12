<?php
require 'header.php';
redirectIfNotLoggedIn();

// Inicijalizuj promenljive na početku
$success_message = null;
$recipe_link = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars(trim($_POST['title']));
    $description = htmlspecialchars(trim($_POST['description']));
    $instructions = htmlspecialchars(trim($_POST['instructions']));
    $ingredients = $_POST['ingredients'] ?? [];
    $quantities = $_POST['quantities'] ?? [];

    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        if (in_array(strtolower($ext), $allowed) && $_FILES['image']['size'] <= 2 * 1024 * 1024) {
            $image = uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], "uploads/$image");
        } else {
            $error = "Nevalidna slika.";
        }
    }

    if (!isset($error)) {
        $stmt = $pdo->prepare("INSERT INTO recipes (title, description, instructions, image, user_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$title, $description, $instructions, $image, $_SESSION['user_id']]);
        $recipe_id = $pdo->lastInsertId();

        foreach ($ingredients as $index => $ingredient_name) {
            if ($ingredient_name) {
                $ingredient_name = htmlspecialchars(trim($ingredient_name));
                $stmt = $pdo->prepare("INSERT INTO ingredients (name) VALUES (?) ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)");
                $stmt->execute([$ingredient_name]);
                $ingredient_id = $pdo->lastInsertId();

                $quantity = $quantities[$index] ?? null;
                $stmt = $pdo->prepare("INSERT INTO recipe_ingredient (recipe_id, ingredient_id, quantity) VALUES (?, ?, ?)");
                $stmt->execute([$recipe_id, $ingredient_id, $quantity]);
            }
        }

        $success_message = "Recept '$title' je uspješno kreiran!";
        $recipe_link = "recipe_show.php?id=$recipe_id";
    }
}
?>

<section class="container">
    <?php if ($success_message !== null): ?>
        <div class="success-message">
            <h2>Uspjeh!</h2>
            <p><?php echo $success_message; ?></p>
            <a href="<?php echo $recipe_link; ?>" class="btn" >Pogledaj recept</a>
            <a href="recipe_create.php" class="btn">Dodaj novi recept</a>
        </div>
    <?php else: ?>
        <form action="recipe_create.php" method="POST" enctype="multipart-form-data">
            <h2>Dodaj recept</h2>
            <?php if (isset($error)): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
            <label for="title">Naslov</label>
            <input type="text" name="title" id="title" required>
            <label for="description">Opis</label>
            <textarea name="description" id="description" required></textarea>
            <label for="instructions">Uputstva</label>
            <textarea name="instructions" id="instructions" required></textarea>
            <label for="image">Slika recepta</label>
            <input type="file" name="image" id="image" accept="image/*">
            <label>Sastojci</label>
            <div class="flex mb-2">
                <input type="text" id="ingredientInput" class="w-1/2 p-2 border rounded mr-2" placeholder="Sastojak">
                <input type="text" id="quantityInput" class="w-1/2 p-2 border rounded mr-2" placeholder="Količina (npr. 200g)">
                <button type="button" onclick="addIngredientCreate()" class="text-orange-500 hover:underline">+ Dodaj sastojak</button>
            </div>
            <div id="selectedIngredients" class="mb-2"></div>
            <div id="ingredientsList">
                <!-- Ovde će se dodavati skriveni inputi za slanje sastojaka i količina -->
            </div>
            <button type="submit">Sačuvaj</button>
        </form>
    <?php endif; ?>
</section>

<script src="/cook/js/scripts.js"></script>
<?php require 'footer.php'; ?>
</body>
</html>