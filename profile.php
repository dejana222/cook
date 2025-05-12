<?php
require 'header.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$stmt = $pdo->prepare("SELECT r.*, COALESCE(AVG(rt.rating), 0) AS avg_rating, COUNT(rt.id) AS rating_count
                       FROM recipes r
                       LEFT JOIN ratings rt ON r.id = rt.recipe_id
                       WHERE r.user_id = ?
                       GROUP BY r.id
                       ORDER BY r.created_at DESC");
$stmt->execute([$user_id]);
$user_recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="container">
    <h2 class="text-3xl font-bold mb-6 text-center">Moj profil</h2>
    <div class="profile-info">
        <?php if ($user): ?>
            <p><strong>Korisničko ime:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <div class="profile-actions">
                <a href="logout.php" class="logout-btn">Odjavi se</a>
                <a href="#delete-account" class="delete-account-btn" onclick="toggleDeleteAccount()">Obriši nalog</a>
            </div>

            <!-- Forma za brisanje naloga -->
            <form action="delete_account.php" method="POST" class="delete-account-form" style="display: none;">
                <h3 class="text-2xl font-bold mb-4">Obriši nalog</h3>
                <p class="text-red-400 mb-4">Da li ste sigurni da želite da obrišete svoj nalog? Ova akcija je nepovratna.</p>
                <label for="delete_password">Unesite lozinku za potvrdu:</label>
                <input type="password" id="delete_password" name="delete_password" required>
                <button type="submit" class="delete-btn">Obriši nalog</button>
            </form>
        <?php else: ?>
            <p class="text-center text-gray-400">Došlo je do greške prilikom učitavanja podataka o korisniku.</p>
        <?php endif; ?>
    </div>
    <h3 class="text-2xl font-bold mb-4">Moji recepti</h3>
    <div class="recipe-grid">
        <?php if (empty($user_recipes)): ?>
            <p class="text-center text-gray-400">Niste dodali nijedan recept.</p>
        <?php else: ?>
            <?php foreach ($user_recipes as $recipe): ?>
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

<script>
function toggleDeleteAccount() {
    const form = document.querySelector('.delete-account-form');
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
}
</script>

<?php require 'footer.php'; ?>