<?php
require_once __DIR__ . '/../src/repositories/filmRepository.php';
require_once __DIR__ . '/../src/repositories/favoriRepository.php';
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

$isFavori = false;
if ($film !== false && isset($_SESSION['utilisateur'])) {
    $isFavori = isFilmFavori((int) $_SESSION['utilisateur']['id'], (int) $film['id']);
}
?>

<div class="detail-container">
    <a href="index.php" class="back-link">
        <i data-lucide="arrow-left" class="back-icon"></i>
        Retour au catalogue
    </a>

    <?php if ($messageErreur !== ''): ?>
        <section class="error-container">
            <h1 class="error-title">Film introuvable</h1>
            <p class="error-text"><?= htmlspecialchars($messageErreur) ?></p>
            <a href="index.php" class="btn-booking">Explorer le catalogue</a>
        </section>
    <?php else: ?>
        <section class="movie-detail-card">
            <div class="movie-detail-image-wrap">
                <img src="<?= htmlspecialchars($film['image']) ?>" alt="Affiche de <?= htmlspecialchars($film['titre']) ?>"
                    class="movie-detail-image">
            </div>

            <div class="movie-detail-content">
                <div class="movie-detail-badges">
                    <span class="badge-country-detail"><?= htmlspecialchars(getCountryCode($film['pays'])) ?></span>
                    <span class="separator-dot">•</span>
                    <span class="movie-detail-meta-text"><?= htmlspecialchars($film['genre']) ?></span>
                    <span class="separator-dot">•</span>
                    <span
                        class="movie-detail-meta-text"><?= htmlspecialchars(date('Y', strtotime($film['date_sortie']))) ?></span>
                </div>

                <h1 class="movie-detail-title"><?= htmlspecialchars($film['titre']) ?></h1>

                <div class="movie-detail-duration">
                    <i data-lucide="clock-3" class="duration-icon"></i>
                    <span><?= htmlspecialchars(convertirDuree((int) $film['duree'])) ?></span>
                </div>

                <div class="synopsis-section">
                    <h2 class="synopsis-title">Synopsis</h2>
                    <p class="synopsis-text"><?= nl2br(htmlspecialchars($film['synopsis'])) ?></p>
                </div>

                <div class="detail-actions">
                    <a href="index.php" class="btn-booking">Retour à la liste</a>

                    <?php if (isset($_SESSION['utilisateur'])): ?>
                        <form method="POST" action="toggle-favori.php" class="favori-form">
                            <input type="hidden" name="film_id" value="<?= (int) $film['id'] ?>">
                            <input type="hidden" name="redirect" value="<?= htmlspecialchars('detail-film.php?id=' . urlencode((string) $film['id'])) ?>">
                            <button type="submit" class="btn-favori <?= $isFavori ? 'btn-favori--active' : '' ?>">
                                <i data-lucide="heart"></i>
                                <?= $isFavori ? 'Retirer des favoris' : 'Ajouter aux favoris' ?>
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../src/includes/footer.php'; ?>