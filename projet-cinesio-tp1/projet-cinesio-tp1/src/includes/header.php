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
    <?php $pageCourante = basename($_SERVER['PHP_SELF']); ?>
    <header class="header">
        <div class="header-container">
            <a href="index.php" class="logo">
                <i data-lucide="clapperboard" class="logo-icon-lib"></i>
                <span class="logo-text"><span class="cine">Cine</span><span class="sio">SIO</span></span>
            </a>
            <nav class="nav-links">
                <a href="index.php" class="<?= ($pageCourante === 'index.php' || $pageCourante === 'detail-film.php') ? 'active' : '' ?>">Catalogue</a>
                <a href="ajouter-film.php" class="<?= $pageCourante === 'ajouter-film.php' ? 'active' : '' ?>">Ajouter un film</a>
                <a href="#">Contact</a>
            </nav>
        </div>
    </header>
    <main class="main-content">
