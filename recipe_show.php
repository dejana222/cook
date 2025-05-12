<?php
require 'header.php';
require 'helpers.php'; 

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$recipe_id = (int)$_GET['id'];

// Dohvatanje recepta
$stmt = $pdo->prepare("SELECT r.*, AVG(rt.rating) as avg_rating, COUNT(rt.id) as rating_count 
                       FROM recipes r 
                       LEFT JOIN ratings rt ON r.id = rt.recipe_id 
                       WHERE r.id = ? 
                       GROUP BY r.id");
$stmt->execute([$recipe_id]);
$recipe = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$recipe) {
    header('Location: index.php');
    exit;
}

// Dohvatanje sastojaka recepta
$stmt = $pdo->prepare("SELECT i.name, ri.quantity 
                       FROM recipe_ingredient ri 
                       JOIN ingredients i ON ri.ingredient_id = i.id 
                       WHERE ri.recipe_id = ?");
$stmt->execute([$recipe_id]);
$ingredients = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Dohvatanje komentara
$stmt = $pdo->prepare("SELECT c.*, u.name AS name 
                       FROM comments c 
                       JOIN users u ON c.user_id = u.id 
                       WHERE c.recipe_id = ? 
                       ORDER BY c.created_at DESC");
$stmt->execute([$recipe_id]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Provera da li je recept u omiljenima i da li je korisnik već ocenio recept
$is_favorite = false;
$user_rating = 0;
$has_rated = false;
$is_author = false;
if (isLoggedIn()) {
    $user_id = $_SESSION['user_id'];
    $is_author = ($recipe['user_id'] == $user_id); // Provera da li je korisnik autor

    // Provera da li je korisnik već ocenio recept
    $stmt = $pdo->prepare("SELECT rating FROM ratings WHERE recipe_id = ? AND user_id = ?");
    $stmt->execute([$recipe_id, $user_id]);
    $rating = $stmt->fetch();
    if ($rating) {
        $has_rated = true;
        $user_rating = $rating['rating'];
    }

    // Proveravamo da li je recept već u omiljenima
    $stmt = $pdo->prepare("SELECT id FROM favorites WHERE user_id = ? AND recipe_id = ?");
    $stmt->execute([$user_id, $recipe_id]);
    $is_favorite = $stmt->fetch() !== false;
}

?>

    <section class="container">
        <div class="recipe-details">
            <h2><?php echo htmlspecialchars($recipe['title']); ?></h2>
            
            <img src="<?php echo $recipe['image'] ? '/cook/uploads/' . htmlspecialchars($recipe['image']) : 'https://via.placeholder.com/600x400.png?text=Nema+slike'; ?>" alt="<?php echo htmlspecialchars($recipe['title']); ?>">
            <div class="rating">
            <?php if (isLoggedIn() && $is_author): ?>
    <a href="recipe_edit.php?id=<?php echo $recipe_id; ?>" class="edit-btn">Uredi recept</a>
<?php endif; ?>
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <svg fill="<?php echo $i <= round($recipe['avg_rating']) ? '#ffd700' : '#ccc'; ?>" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.97a1 1 0 00.95.69h4.178c.969 0 1.371 1.24.588 1.81l-3.39 2.467a1 1 0 00-.364 1.118l1.286 3.97c.3.921-.755 1.688-1.54 1.118l-3.39-2.467a1 1 0 00-1.175 0l-3.39 2.467c-.784.57-1.838-.197-1.54-1.118l1.286-3.97a1 1 0 00-.364-1.118L2.737 9.397c-.783-.57-.38-1.81.588-1.81h4.178a1 1 0 00.95-.69l1.286-3.97z"/>
                    </svg>
                <?php endfor; ?>
                <span>(<?php echo $recipe['rating_count']; ?>)</span>
                <?php if (isLoggedIn()): ?>
                    <button id="favorite-btn" class="favorite-btn" data-recipe-id="<?php echo $recipe_id; ?>" style="background: none; border: none; cursor: pointer;">
                        <svg id="favorite-icon" fill="<?php echo $is_favorite ? '#ff6200' : '#ccc'; ?>" viewBox="0 0 20 20" width="24" height="24">
                            <path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"/>
                        </svg>
                    </button>
                <?php endif; ?>
            </div>
            <p><?php echo htmlspecialchars($recipe['description']); ?></p>
            <h3>Sastojci</h3>
            <ul>
                <?php foreach ($ingredients as $ingredient): ?>
                    <li><span><?php echo htmlspecialchars($ingredient['name']); ?></span> - <?php echo htmlspecialchars($ingredient['quantity'] ?? 'Po ukusu'); ?></li>
                <?php endforeach; ?>
            </ul>
            <h3>Uputstva</h3>
            <ol>
                <?php foreach (explode("\n", $recipe['steps'] ?? $recipe['instructions']) as $step): ?>
                    <?php if (trim($step)): ?>
                        <li><?php echo htmlspecialchars($step); ?></li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ol>

            <?php if (isLoggedIn()): ?>
    <div class="rating-form">
        <?php if ($has_rated): ?>
            <p class="user-rating">Vaša ocjena: 
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <svg class="rating-star" fill="<?php echo $i <= $user_rating ? '#ffd700' : '#ccc'; ?>" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.97a1 1 0 00.95.69h4.178c.969 0 1.371 1.24.588 1.81l-3.39 2.467a1 1 0 00-.364 1.118l1.286 3.97c.3.921-.755 1.688-1.54 1.118l-3.39-2.467a1 1 0 00-1.175 0l-3.39 2.467c-.784.57-1.838-.197-1.54-1.118l1.286-3.97a1 1 0 00-.364-1.118L2.737 9.397c-.783-.57-.38-1.81.588-1.81h4.178a1 1 0 00.95-.69l1.286-3.97z"/>
                    </svg>
                <?php endfor; ?>
            </p>
        <?php else: ?>
            <div class="rate-recipe">
                <span class="rate-label">Ocijenite:</span>
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <svg class="rating-star" data-value="<?php echo $i; ?>" fill="#ccc" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.97a1 1 0 00.95.69h4.178c.969 0 1.371 1.24.588 1.81l-3.39 2.467a1 1 0 00-.364 1.118l1.286 3.97c.3.921-.755 1.688-1.54 1.118l-3.39-2.467a1 1 0 00-1.175 0l-3.39 2.467c-.784.57-1.838-.197-1.54-1.118l1.286-3.97a1 1 0 00-.364-1.118L2.737 9.397c-.783-.57-.38-1.81.588-1.81h4.178a1 1 0 00.95-.69l1.286-3.97z"/>
                    </svg>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>
    <div class="share-buttons">
    <h4>Podijeli recept:</h4>
    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . '/cook/recipe_show.php?id=' . $recipe_id); ?>" target="_blank">
        <svg fill="#ffffff" viewBox="0 0 24 24" width="24" height="24">
            <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/>
        </svg>
    </a>
    <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . '/cook/recipe_show.php?id=' . $recipe_id); ?>&text=Probaj ovaj recept: <?php echo htmlspecialchars($recipe['title']); ?>" target="_blank">
        <svg fill="#ffffff" viewBox="0 0 24 24" width="24" height="24">
            <path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/>
        </svg>
    </a>
    <a href="mailto:?subject=Probaj ovaj recept: <?php echo htmlspecialchars($recipe['title']); ?>&body=Pogledaj ovaj recept: <?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . '/cook/recipe_show.php?id=' . $recipe_id); ?>" target="_blank">
        <svg fill="#ffffff" viewBox="0 0 24 24" width="24" height="24">
            <path d="M20 4H4a2 2 0 00-2 2v12a2 2 0 002 2h16a2 2 0 002-2V6a2 2 0 00-2-2zm0 4l-8 5-8-5V6l8 5 8-5z"/>
        </svg>
    </a>
</div>



                <div class="comments">
                    <h3>Komentari</h3>
                    <form action="comment.php" method="POST">
                        <input type="hidden" name="recipe_id" value="<?php echo $recipe_id; ?>">
                        <label for="content">Dodaj komentar</label>
                        <textarea name="content" id="content" required></textarea>
                        <button type="submit">Pošalji</button>
                    </form>

                    <?php if (empty($comments)): ?>
                        <p class="text-gray-400">Nema komentara.</p>
                    <?php else: ?>
                        <?php foreach ($comments as $comment): ?>
                            <div class="comment-card">
                                <div class="author"><?php echo htmlspecialchars($comment['name']); ?></div>
                                <div class="date"><?php echo date('d.m.Y H:i', strtotime($comment['created_at'])); ?></div>
                                <p><?php echo htmlspecialchars($comment['content']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <p><a href="login.php">Prijavi se</a> da bi ocijenio recept ili ostavio komentar.</p>
            <?php endif; ?>
        </div>
    </section>

    <script>
        document.getElementById('favorite-btn')?.addEventListener('click', function() {
            const recipeId = this.getAttribute('data-recipe-id');
            const icon = document.getElementById('favorite-icon');

            fetch('/cook/toggle_favorite.php', { // Apsolutna putanja
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `recipe_id=${recipeId}`
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP greška! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.is_favorite) {
                    icon.setAttribute('fill', '#ff6200'); // Ispunjeno srce
                    alert('Recept je dodat u omiljene!');
                } else {
                    icon.setAttribute('fill', '#ccc'); // Prazno srce
                    alert('Recept je uklonjen iz omiljenih.');
                }
            })
            .catch(error => {
                console.error('Greška:', error);
                alert('Došlo je do greške prilikom označavanja omiljenog recepta: ' + error.message);
            });
        });
    </script>
<script>
document.querySelectorAll('.rating-star').forEach(star => {
    star.addEventListener('click', function() {
        const rating = this.getAttribute('data-value');
        const recipeId = <?php echo $recipe_id; ?>;

        fetch('/cook/rate.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `recipe_id=${recipeId}&rating=${rating}`
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            location.reload(); // Osveži stranicu nakon ocenjivanja
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Došlo je do greške prilikom ocenjivanja recepta.');
        });
    });
});
</script>
<?php require 'footer.php'; ?>