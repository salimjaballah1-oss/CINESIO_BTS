<?php
require_once __DIR__ . '/../src/includes/header.php';
require_once __DIR__ . '/../src/repositories/favoriRepository.php';
require_once __DIR__ . '/../src/lib/functions.php';

if (!isset($_SESSION['utilisateur'])) {
    header('Location: connexion.php');
    exit;
}

$utilisateurId = (int) $_SESSION['utilisateur']['id'];
$filmsFavoris = findFavorisFilmsByUtilisateurId($utilisateurId);
?>

<div class="page-header">
    <h2 class="page-title">Mes favoris</h2>
    <p class="page-subtitle">
        <?php if (count($filmsFavoris) === 0): ?>
            Tu n'as pas encore de film favori.
        <?php elseif (count($filmsFavoris) === 1): ?>
            1 film dans tes favoris.
        <?php else: ?>
            <span><?= count($filmsFavoris) ?></span> films dans tes favoris.
        <?php endif; ?>
    </p>
</div>

<?php if (count($filmsFavoris) === 0): ?>
    <div class="detail-container">
        <section class="error-container">
            <h1 class="error-title">Aucun favori</h1>
            <p class="error-text">Ajoute des films en favori depuis la page détail d'un film.</p>
            <a href="index.php" class="btn-booking">Explorer le catalogue</a>
        </section>
    </div>
<?php else: ?>
    <div class="carousel-wrap">
        <button class="carousel-btn" type="button" data-dir="-1" aria-label="Précédent">
            <i data-lucide="chevron-left"></i>
        </button>

        <div class="carousel" id="favorisCarousel">
            <?php foreach ($filmsFavoris as $film): ?>
                <div class="card carousel-card">
                    <div class="card-image-wrap">
                        <a href="detail-film.php?id=<?= urlencode($film['id']) ?>">
                            <img src="<?= htmlspecialchars($film['image']) ?>" alt="Affiche de <?= htmlspecialchars($film['titre']) ?>" class="card-image">
                        </a>
                        <span class="badge-country"><?= getCountryCode($film['pays']) ?></span>
                    </div>
                    <div class="card-content">
                        <h3 class="card-title">
                            <a href="detail-film.php?id=<?= urlencode($film['id']) ?>" style="text-decoration: none; color: inherit;">
                                <?= htmlspecialchars($film['titre']) ?>
                            </a>
                        </h3>
                        <div class="card-meta">
                            <?= htmlspecialchars($film['genre']) ?> &bull; <?= convertirDuree((int) $film['duree']) ?>
                        </div>
                        <p class="card-synopsis"><?= htmlspecialchars($film['synopsis']) ?></p>
                        <div class="favori-actions">
                            <a href="detail-film.php?id=<?= urlencode($film['id']) ?>" class="btn-primary">D&eacute;tails</a>
                            <form method="POST" action="toggle-favori.php" class="favori-form">
                                <input type="hidden" name="film_id" value="<?= (int) $film['id'] ?>">
                                <input type="hidden" name="redirect" value="favoris.php">
                                <button type="submit" class="btn-favori btn-favori--active" title="Retirer des favoris">
                                    <i data-lucide="heart"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <button class="carousel-btn" type="button" data-dir="1" aria-label="Suivant">
            <i data-lucide="chevron-right"></i>
        </button>
    </div>

    <script>
        (function () {
            const carousel = document.getElementById('favorisCarousel');
            if (!carousel) return;

            const buttons = document.querySelectorAll('.carousel-btn');
            buttons.forEach((btn) => {
                btn.addEventListener('click', () => {
                    const dir = Number(btn.getAttribute('data-dir') || '0');
                    const firstCard = carousel.querySelector('.carousel-card');
                    const cardWidth = firstCard ? firstCard.getBoundingClientRect().width : 260;
                    carousel.scrollBy({ left: dir * (cardWidth + 16), behavior: 'smooth' });
                });
            });
        })();
    </script>
<?php endif; ?>

<?php require_once __DIR__ . '/../src/includes/footer.php'; ?>

