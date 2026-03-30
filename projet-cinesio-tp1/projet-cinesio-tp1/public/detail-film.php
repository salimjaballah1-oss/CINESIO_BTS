<?php
require_once __DIR__ . '/../src/repositories/filmRepository.php';
require_once __DIR__ . '/../src/lib/functions.php';

$id = $_GET['id'] ?? '';
$messageErreur = '';
$film = false;

if ($id === '') {
    $messageErreur = "Désolé, le film que vous recherchez n'existe pas ou n'est plus disponible dans notre catalogue.";
} elseif (filter_var($id, FILTER_VALIDATE_INT) === false) {
    $messageErreur = "Désolé, le film que vous recherchez n'existe pas ou n'est plus disponible dans notre catalogue.";
} elseif ((int) $id <= 0) {
    $messageErreur = "Désolé, le film que vous recherchez n'existe pas ou n'est plus disponible dans notre catalogue.";
} else {
    $id = (int) $id;
    $film = findFilmById($id);

    if ($film === false) {
        $messageErreur = "Désolé, le film que vous recherchez n'existe pas ou n'est plus disponible dans notre catalogue.";
    }
}

include __DIR__ . '/../src/includes/header.php';
?>

<div class="detail-container">
    <a href="index.php" class="back-link">
        <i data-lucide="arrow-left" class="back-icon"></i>
        Retour au catalogue
    </a>

    <?php if ($messageErreur !== ''): ?>
        <section class="error-container">
            <h1 class="error-title">Film introuvable</h1>
            <p class="error-text"><?= htmlspecialchars($messageErreur, ENT_QUOTES, 'UTF-8') ?></p>
            <a href="index.php" class="btn-booking">Explorer le catalogue</a>
        </section>
    <?php else: ?>
        <section class="movie-detail-card">
            <div class="movie-detail-image-wrap">
                <img src="<?= htmlspecialchars($film['image'], ENT_QUOTES, 'UTF-8') ?>" alt="Affiche de <?= htmlspecialchars($film['titre'], ENT_QUOTES, 'UTF-8') ?>" class="movie-detail-image">
            </div>

            <div class="movie-detail-content">
                <div class="movie-detail-badges">
                    <span class="badge-country-detail"><?= htmlspecialchars(getCountryCode($film['pays']), ENT_QUOTES, 'UTF-8') ?></span>
                    <span class="separator-dot">•</span>
                    <span class="movie-detail-meta-text"><?= htmlspecialchars($film['genre'], ENT_QUOTES, 'UTF-8') ?></span>
                    <span class="separator-dot">•</span>
                    <span class="movie-detail-meta-text"><?= htmlspecialchars(date('Y', strtotime($film['date_sortie'])), ENT_QUOTES, 'UTF-8') ?></span>
                </div>

                <h1 class="movie-detail-title"><?= htmlspecialchars($film['titre'], ENT_QUOTES, 'UTF-8') ?></h1>

                <div class="movie-detail-duration">
                    <i data-lucide="clock-3" class="duration-icon"></i>
                    <span><?= htmlspecialchars(convertirDuree((int) $film['duree']), ENT_QUOTES, 'UTF-8') ?></span>
                </div>

                <div class="synopsis-section">
                    <h2 class="synopsis-title">Synopsis</h2>
                    <p class="synopsis-text"><?= nl2br(htmlspecialchars($film['synopsis'], ENT_QUOTES, 'UTF-8')) ?></p>
                </div>

                <a href="index.php" class="btn-booking">Retour à la liste</a>
            </div>
        </section>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../src/includes/footer.php'; ?>
