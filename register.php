<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $password]);
        header('Location: login.php');
        exit;
    } catch (PDOException $e) {
        $error = "Email već postoji.";
    }
}
?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuvajmo - Registracija</title>
    <link href="/cook/css/style.css" rel="stylesheet">
</head>
<body>
    <nav>
        <div class="container">
            <h1>Kuvajmo</h1>
            <div>
                <a href="index.php">Početna</a>
                <a href="login.php">Prijava</a>
            </div>
        </div>
    </nav>

    <section class="container">
        <form action="register.php" method="POST">
            <h2>Registracija</h2>
            <?php if (isset($error)): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
            <label for="name">Ime</label>
            <input type="text" name="name" id="name" required>
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required>
            <label for="password">Lozinka</label>
            <input type="password" name="password" id="password" required>
            <button type="submit">Registruj se</button>
        </form>
    </section>
    <?php require 'footer.php'; ?>
</body>
</html>