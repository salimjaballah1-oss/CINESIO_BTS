<?php
require_once __DIR__ . '/../src/repositories/filmRepository.php';

$genres = findAllGenres();
$paysListe = findAllPays();

$film = [
    'titre' => '',
    'date_sortie' => '',
    'duree' => '',
    'image' => '',
    'synopsis' => '',
    'id_genre' => '',
    'id_pays' => '',
];

$erreurs = [];
$messageSucces = '';
$messageErreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $film['titre'] = trim($_POST['titre'] ?? '');
    $film['date_sortie'] = trim($_POST['date_sortie'] ?? '');
    $film['duree'] = trim($_POST['duree'] ?? '');
    $film['image'] = trim($_POST['image'] ?? '');
    $film['synopsis'] = trim($_POST['synopsis'] ?? '');
    $film['id_genre'] = trim($_POST['id_genre'] ?? '');
    $film['id_pays'] = trim($_POST['id_pays'] ?? '');

    if ($film['titre'] === '') {
        $erreurs['titre'] = "Le titre est obligatoire.";
    } elseif (strlen($film['titre']) < 2) {
        $erreurs['titre'] = "Le titre doit contenir au moins 2 caracteres.";
    }

    if ($film['date_sortie'] === '') {
        $erreurs['date_sortie'] = "La date de sortie est obligatoire.";
    }

    if ($film['duree'] === '') {
        $erreurs['duree'] = "La duree est obligatoire.";
    } elseif (filter_var($film['duree'], FILTER_VALIDATE_INT) === false) {
        $erreurs['duree'] = "La duree doit etre un nombre entier.";
    } elseif ((int) $film['duree'] <= 0) {
        $erreurs['duree'] = "La duree doit etre superieure a 0.";
    }

    if ($film['image'] === '') {
        $erreurs['image'] = "L'URL de l'image est obligatoire.";
    } elseif (filter_var($film['image'], FILTER_VALIDATE_URL) === false) {
        $erreurs['image'] = "L'URL de l'image n'est pas valide.";
    }

    if ($film['synopsis'] === '') {
        $erreurs['synopsis'] = "Le synopsis est obligatoire.";
    } elseif (strlen($film['synopsis']) < 10) {
        $erreurs['synopsis'] = "Le synopsis doit contenir au moins 10 caracteres.";
    }

    if ($film['id_genre'] === '') {
        $erreurs['id_genre'] = "Le genre est obligatoire.";
    }

    if ($film['id_pays'] === '') {
        $erreurs['id_pays'] = "Le pays est obligatoire.";
    }

    $idsGenres = array_map('intval', array_column($genres, 'id'));
    $idsPays = array_map('intval', array_column($paysListe, 'id'));

    if ($film['id_genre'] !== '' && !in_array((int) $film['id_genre'], $idsGenres, true)) {
        $erreurs['id_genre'] = "Le genre selectionne est invalide.";
    }

    if ($film['id_pays'] !== '' && !in_array((int) $film['id_pays'], $idsPays, true)) {
        $erreurs['id_pays'] = "Le pays selectionne est invalide.";
    }

    if (empty($erreurs)) {
        $filmData = [
            'titre' => $film['titre'],
            'date_sortie' => $film['date_sortie'],
            'duree' => (int) $film['duree'],
            'synopsis' => $film['synopsis'],
            'image' => $film['image'],
            'id_genre' => (int) $film['id_genre'],
            'id_pays' => (int) $film['id_pays'],
        ];

        $insertionOk = insertFilm($filmData);

        if ($insertionOk) {
            $messageSucces = "Le film a ete ajoute avec succes.";
            $film = [
                'titre' => '',
                'date_sortie' => '',
                'duree' => '',
                'image' => '',
                'synopsis' => '',
                'id_genre' => '',
                'id_pays' => '',
            ];
        } else {
            $messageErreur = "Une erreur est survenue lors de l'ajout du film.";
        }
    }
}

include __DIR__ . '/../src/includes/header.php';
?>

<div class="form-page">
    <div class="page-header form-page-header">
        <h2 class="page-title">Ajouter un nouveau film</h2>
        <p class="page-subtitle">Veuillez renseigner les informations ci-dessous pour ajouter un film au catalogue CineSIO.</p>
    </div>

    <div class="form-card">
        <?php if ($messageSucces !== ''): ?>
            <div class="form-alert form-alert-success"><?= htmlspecialchars($messageSucces, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <?php if ($messageErreur !== ''): ?>
            <div class="form-alert form-alert-error"><?= htmlspecialchars($messageErreur, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <form action="ajouter-film.php" method="POST" class="movie-form">
            <div class="form-group">
                <label for="titre">Titre du film <span class="required-mark">*</span></label>
                <input
                    type="text"
                    id="titre"
                    name="titre"
                    value="<?= htmlspecialchars($film['titre'], ENT_QUOTES, 'UTF-8') ?>"
                    placeholder="Ex: Dune: Deuxieme Partie"
                >
                <?php if (isset($erreurs['titre'])): ?>
                    <p class="form-error"><?= htmlspecialchars($erreurs['titre'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="date_sortie">Date de sortie <span class="required-mark">*</span></label>
                    <input
                        type="date"
                        id="date_sortie"
                        name="date_sortie"
                        value="<?= htmlspecialchars($film['date_sortie'], ENT_QUOTES, 'UTF-8') ?>"
                    >
                    <?php if (isset($erreurs['date_sortie'])): ?>
                        <p class="form-error"><?= htmlspecialchars($erreurs['date_sortie'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="duree">Duree (en minutes) <span class="required-mark">*</span></label>
                    <input
                        type="number"
                        id="duree"
                        name="duree"
                        value="<?= htmlspecialchars($film['duree'], ENT_QUOTES, 'UTF-8') ?>"
                        min="1"
                        placeholder="Ex: 166"
                    >
                    <?php if (isset($erreurs['duree'])): ?>
                        <p class="form-error"><?= htmlspecialchars($erreurs['duree'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group">
                <label for="synopsis">Synopsis <span class="required-mark">*</span></label>
                <textarea
                    id="synopsis"
                    name="synopsis"
                    rows="6"
                    placeholder="Le heros commence son periple..."
                ><?= htmlspecialchars($film['synopsis'], ENT_QUOTES, 'UTF-8') ?></textarea>
                <?php if (isset($erreurs['synopsis'])): ?>
                    <p class="form-error"><?= htmlspecialchars($erreurs['synopsis'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="image">Affiche web (URL de l'image) <span class="required-mark">*</span></label>
                <input
                    type="text"
                    id="image"
                    name="image"
                    value="<?= htmlspecialchars($film['image'], ENT_QUOTES, 'UTF-8') ?>"
                    placeholder="https://exemple.com/image.jpg"
                >
                <?php if (isset($erreurs['image'])): ?>
                    <p class="form-error"><?= htmlspecialchars($erreurs['image'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="id_genre">Genre <span class="required-mark">*</span></label>
                    <select id="id_genre" name="id_genre">
                        <option value="">Selectionnez un genre...</option>
                        <?php foreach ($genres as $genre): ?>
                            <option
                                value="<?= htmlspecialchars((string) $genre['id'], ENT_QUOTES, 'UTF-8') ?>"
                                <?= $film['id_genre'] === (string) $genre['id'] ? 'selected' : '' ?>
                            >
                                <?= htmlspecialchars($genre['nom'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($erreurs['id_genre'])): ?>
                        <p class="form-error"><?= htmlspecialchars($erreurs['id_genre'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="id_pays">Pays <span class="required-mark">*</span></label>
                    <select id="id_pays" name="id_pays">
                        <option value="">Selectionnez un pays...</option>
                        <?php foreach ($paysListe as $pays): ?>
                            <option
                                value="<?= htmlspecialchars((string) $pays['id'], ENT_QUOTES, 'UTF-8') ?>"
                                <?= $film['id_pays'] === (string) $pays['id'] ? 'selected' : '' ?>
                            >
                                <?= htmlspecialchars($pays['nom'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($erreurs['id_pays'])): ?>
                        <p class="form-error"><?= htmlspecialchars($erreurs['id_pays'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <button type="submit" class="btn-primary btn-submit-film">
                <i data-lucide="plus-circle"></i>
                Ajouter ce film au catalogue
            </button>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../src/includes/footer.php'; ?>
