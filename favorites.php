<?php
require 'header.php';
redirectIfNotLoggedIn();

// Dohvatanje omiljenih recepata trenutnog korisnika
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT r.*, AVG(rt.rating) as avg_rating, COUNT(rt.id) as rating_count 
                       FROM recipes r 
                       JOIN favorites f ON r.id = f.recipe_id 
                       LEFT JOIN ratings rt ON r.id = rt.recipe_id 
                       WHERE f.user_id = ? 
                       GROUP BY r.id 
                       ORDER BY f.created_at DESC");
$stmt->execute([$user_id]);
$favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

    <section class="container">
        <h2 class="text-3xl font-bold mb-6 text-center">Omiljeni recepti</h2>
        <div id="recipes" class="recipe-grid">
            <?php if (empty($favorites)): ?>
                <p class="text-center text-gray-400">Trenutno nema omiljenih recepata.</p>
            <?php else: ?>
                <?php foreach ($favorites as $recipe): ?>
                    <div class="recipe-card">
                        <img src="<?php echo $recipe['image'] ? '/cook/uploads/' . htmlspecialchars($recipe['image']) : 'https://via.placeholder.com/300x220.png?text=Nema+slike'; ?>" alt="<?php echo htmlspecialchars($recipe['title']); ?>">
                        <div class="recipe-card-content">
                            <h3><?php echo htmlspecialchars($recipe['title']); ?></h3>
                            <p><?php echo htmlspecialchars(substr($recipe['description'], 0, 100)); ?>...</p>
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

<?php require 'footer.php'; ?>