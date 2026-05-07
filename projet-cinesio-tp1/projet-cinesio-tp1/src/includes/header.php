<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$pageCourante = basename($_SERVER['PHP_SELF']); 
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CineSIO - Catalogue de films</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/lucide@latest/dist/umd/lucide.min.js"></script>
</head>

<body>
    <header class="header">
        <div class="header-container">
            <a href="index.php" class="logo">
                <i data-lucide="clapperboard" class="logo-icon-lib"></i>
                <span class="logo-text"><span class="cine">Cine</span><span class="sio">SIO</span></span>
            </a>
            <nav class="nav-links">
                <a href="index.php" class="<?= ($pageCourante === 'index.php' || $pageCourante === 'detail-film.php') ? 'active' : '' ?>">Catalogue</a>
                
                <?php if (isset($_SESSION['utilisateur'])): ?>
                    <a href="ajouter-film.php" class="<?= $pageCourante === 'ajouter-film.php' ? 'active' : '' ?>">Ajouter un film</a>
                    <a href="#" class="user-link">
                        <i data-lucide="user" class="nav-icon"></i>
                        <?= htmlspecialchars($_SESSION['utilisateur']['pseudo']) ?>
                    </a>
                    <a href="deconnexion.php" class="logout-link">
                        <i data-lucide="log-out" class="nav-icon"></i>
                    </a>
                <?php else: ?>
                    <a href="inscription.php" class="<?= $pageCourante === 'inscription.php' ? 'active' : '' ?>">Inscription</a>
                    <a href="connexion.php" class="<?= $pageCourante === 'connexion.php' ? 'active' : '' ?>">Connexion</a>
                <?php endif; ?>
                
                <a href="#">Contact</a>
            </nav>
        </div>
    </header>
    <main class="main-content">
