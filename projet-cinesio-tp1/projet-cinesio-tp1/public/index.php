<?php
require_once __DIR__ . '/../src/repositories/filmRepository.php';
require_once __DIR__ . '/../src/lib/functions.php';

$films = findAllFilms();

include __DIR__ . '/../src/includes/header.php';
?>

<div class="page-header">
    <h2 class="page-title">Catalogue des Films</h2>
    <p class="page-subtitle">Il y a actuellement <span><?= count($films) ?></span> films dans le catalogue.</p>
</div>

<div class="movies-grid">
    <?php foreach ($films as $film): ?>
        <div class="card">
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
                    <?= htmlspecialchars($film['genre']) ?> â€¢ <?= convertirDuree($film['duree']) ?>
                </div>

                <p class="card-synopsis">
                    <?= htmlspecialchars($film['synopsis']) ?>
                </p>

                <a href="detail-film.php?id=<?= urlencode($film['id']) ?>" class="btn-primary">Détails</a>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php include __DIR__ . '/../src/includes/footer.php'; ?>

