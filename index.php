<?php
require 'header.php';

// Dohvati 3 najpopularnija recepta
try {
    $stmt = $pdo->query("SELECT r.*, COALESCE(AVG(rt.rating), 0) AS avg_rating, COUNT(rt.id) AS rating_count
                         FROM recipes r
                         LEFT JOIN ratings rt ON r.id = rt.recipe_id
                         GROUP BY r.id
                         ORDER BY avg_rating DESC
                         LIMIT 3");
    $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Log the error if needed, but show a user-friendly message
    $recipes = [];
    $error = "Došlo je do greške prilikom učitavanja popularnih recepata.";
}
?>

<section class="hero">
    <div class="hero-content">
        <h2>Pronađi savršen recept!</h2>
        <p>Unesi sastojke i otkrij šta možeš da spremiš.</p>
        <button onclick="openSearchModal()">Pretraži recepte</button>
    </div>
</section>

<div id="searchModal" class="modal">
    <div class="modal-content">
        <h3>Pretraži recepte</h3>
        <input type="text" id="recipeName" placeholder="Unesi naziv jela">
        <div id="ingredientsSearch">
            <div class="ingredient-input-container">
                <input type="text" id="ingredientInput" placeholder="Unesi sastojak (npr. luk)" onkeypress="if(event.key === 'Enter') addIngredientSearch()">
                <button onclick="addIngredientSearch()" class="add-ingredient-btn">Dodaj</button>
            </div>
            <div id="selectedIngredients" class="mt-2"></div>
        </div>
        <button onclick="searchRecipes()" class="search-btn">Pretraži</button>
        <button onclick="closeSearchModal()" class="close-btn">Zatvori</button>
    </div>
</div>

<section class="container">
    <h2 class="text-3xl font-bold mb-6 text-center">Popularni recepti</h2>
    <div id="recipes" class="recipe-grid">
        <?php if (isset($error)): ?>
            <p class="text-center text-gray-400"><?php echo htmlspecialchars($error); ?></p>
        <?php elseif (empty($recipes)): ?>
            <p class="text-center text-gray-400">Trenutno nema popularnih recepata.</p>
        <?php else: ?>
            <?php foreach ($recipes as $recipe): ?>
                <div class="recipe-card">
                    <img src="<?php echo $recipe['image'] ? '/cook/uploads/' . htmlspecialchars($recipe['image']) : 'https://via.placeholder.com/300x220.png?text=Nema+slike'; ?>" alt="<?php echo htmlspecialchars($recipe['title']); ?>">
                    <div class="recipe-card-content">
                        <h3><?php echo htmlspecialchars($recipe['title']); ?></h3>
                        <p><?php echo htmlspecialchars(mb_substr($recipe['description'], 0, 100, 'UTF-8')); ?>...</p>
                        <div class="rating">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <svg fill="<?php echo $i <= round($recipe['avg_rating']) ? '#ffd700' : '#ccc'; ?>" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.97a1 1 0 00.95.69h4.178c.969 0 1.371 1.24.588 1.81l-3.39 2.467a1 1 0 00-.364 1.118l1.286 3.97c.3.921-.755 1.688-1.54 1.118l-3.39-2.467a1 1 0 00-1.175 0l-3.39 2.467c-.784.57-1.838-.197-1.54-1.118l1.286-3.97a1 1 0 00-.364-1.118L2.737 9.397c-.783-.57-.38-1.81.588-1.81h4.178a1 1 0 00.95-.69l1.286-3.97z"/>
                                </svg>
                            <?php endfor; ?>
                            <span>(<?php echo $recipe['rating_count']; ?>)</span>
                        </div>
                        <a href="recipe_show.php?id=<?php echo $recipe['id']; ?>">Pogledaj recept</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<script src="/cook/js/scripts.js"></script>
<?php require 'footer.php'; ?>