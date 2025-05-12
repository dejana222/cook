<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        header('Location: index.php');
        exit;
    } else {
        $error = "Pogrešan email ili lozinka.";
    }
}
?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuvajmo - Prijava</title>
    <link href="/cook/css/style.css" rel="stylesheet">
</head>
<body>
    <nav>
        <div class="container">
            <h1>Kuvajmo</h1>
            <div>
                <a href="index.php">Početna</a>
                <a href="register.php">Registracija</a>
            </div>
        </div>
    </nav>

    <section class="container">
        <form action="login.php" method="POST">
            <h2>Prijava</h2>
            <?php if (isset($error)): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required>
            <label for="password">Lozinka</label>
            <input type="password" name="password" id="password" required>
            <button type="submit">Prijavi se</button>
        </form>
    </section>
    <?php require 'footer.php'; ?>
</body>
</html>