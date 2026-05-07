<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['utilisateur'])) {
    header('Location: connexion.php');
    exit;
}

require_once __DIR__ . '/../src/repositories/filmRepository.php';

$errors = [];
$fieldErrors = []; 
$success = false;

function getFieldError($field, $errors) {
    foreach ($errors as $error) {
        if (strpos($error, $field) !== false) {
            return $error;
        }
    }
    return null;
}

try {
    $genres = findAllGenres();
    $paysListe = findAllPays();
} catch (PDOException $e) {
    $genres = [];
    $paysListe = [];
    $errors[] = "Erreur critique : Impossible de contacter la base de données pour charger les genres et les pays.";
}


$titre = '';
$date_sortie = '';
$duree = '';
$image = '';
$synopsis = '';
$id_genre = '';
$id_pays = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($errors)) {
    $titre = trim($_POST['titre'] ?? '');
    $date_sortie = trim($_POST['date_sortie'] ?? '');
    $duree = trim($_POST['duree'] ?? '');
    $image = trim($_POST['image'] ?? '');
    $synopsis = trim($_POST['synopsis'] ?? '');
    $id_genre = trim($_POST['id_genre'] ?? '');
    $id_pays = trim($_POST['id_pays'] ?? '');

    if (empty($titre)) {
        $fieldErrors['titre'] = "Le titre est obligatoire.";
    } elseif (strlen($titre) < 2) {
        $fieldErrors['titre'] = "Le titre doit contenir au moins 2 caractères.";
    }

    if (empty($date_sortie)) {
        $fieldErrors['date_sortie'] = "La date de sortie est obligatoire.";
    }

    if (empty($duree)) {
        $fieldErrors['duree'] = "La durée est obligatoire.";
    } elseif (!filter_var($duree, FILTER_VALIDATE_INT)) {
        $fieldErrors['duree'] = "La durée doit être un nombre entier.";
    } elseif ((int) $duree <= 0) {
        $fieldErrors['duree'] = "La durée doit être supérieure à 0.";
    }

    if (empty($image)) {
        $fieldErrors['image'] = "L'URL de l'image est obligatoire.";
    } elseif (!filter_var($image, FILTER_VALIDATE_URL)) {
        $fieldErrors['image'] = "L'URL de l'image n'est pas valide.";
    }

    if (empty($synopsis)) {
        $fieldErrors['synopsis'] = "Le synopsis est obligatoire.";
    } elseif (strlen($synopsis) < 10) {
        $fieldErrors['synopsis'] = "Le synopsis doit contenir au moins 10 caractères.";
    }

    if (empty($id_genre)) {
        $fieldErrors['id_genre'] = "Le genre est obligatoire.";
    }

    if (empty($id_pays)) {
        $fieldErrors['id_pays'] = "Le pays est obligatoire.";
    }

    $idsGenres = array_column($genres, 'id');
    if (!empty($id_genre) && !in_array($id_genre, $idsGenres)) {
        $fieldErrors['id_genre'] = "Le genre sélectionné est invalide.";
    }

    $idsPays = array_column($paysListe, 'id');
    if (!empty($id_pays) && !in_array($id_pays, $idsPays)) {
        $fieldErrors['id_pays'] = "Le pays sélectionné est invalide.";
    }

    if (!empty($fieldErrors)) {
        $errors = $fieldErrors;
    }

    if (empty($fieldErrors)) {
        $filmData = [
            'titre' => $titre,
            'date_sortie' => $date_sortie,
            'duree' => (int) $duree,
            'synopsis' => $synopsis,
            'image' => $image,
            'id_genre' => (int) $id_genre,
            'id_pays' => (int) $id_pays,
        ];

        if (insertFilm($filmData)) {
            $success = true;
            $titre = $date_sortie = $duree = $synopsis = $image = $id_genre = $id_pays = '';
        } else {
            $errors['database'] = "Une erreur est survenue lors de l'ajout du film en base de données.";
        }
    }
}


include __DIR__ . '/../src/includes/header.php';
?>

<div class="form-page">
    <div class="page-header form-page-header">
        <h2 class="page-title">Ajouter un nouveau film</h2>
        <p class="page-subtitle">Veuillez renseigner les informations ci-dessous pour ajouter un film au catalogue
            CineSIO.</p>
    </div>

    <div class="form-card">
        <?php if ($success): ?>
            <div class="form-alert form-alert-success">Le film a été ajouté avec succès !</div>
        <?php endif; ?>

        <?php if (isset($errors['database'])): ?>
            <div class="form-alert form-alert-error">
                <strong><?= htmlspecialchars($errors['database']) ?></strong>
            </div>
        <?php endif; ?>

        <form action="ajouter-film.php" method="POST" class="movie-form" novalidate>
            <div class="form-group">
                <label for="titre">Titre du film <span class="required-mark">*</span></label>
                <input type="text" id="titre" name="titre" value="<?= htmlspecialchars($titre) ?>"
                    placeholder="Ex: Dune: Deuxieme Partie"
                    class="<?= isset($errors['titre']) ? 'input-error' : '' ?>">
                <?php if (isset($errors['titre'])): ?>
                    <span class="form-error"><?= htmlspecialchars($errors['titre']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="date_sortie">Date de sortie <span class="required-mark">*</span></label>
                    <input type="date" id="date_sortie" name="date_sortie"
                        value="<?= htmlspecialchars($date_sortie) ?>"
                        class="<?= isset($errors['date_sortie']) ? 'input-error' : '' ?>">
                    <?php if (isset($errors['date_sortie'])): ?>
                        <span class="form-error"><?= htmlspecialchars($errors['date_sortie']) ?></span>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="duree">Durée (en minutes) <span class="required-mark">*</span></label>
                    <input type="number" id="duree" name="duree"
                        value="<?= htmlspecialchars($duree) ?>" min="1" placeholder="Ex: 166"
                        class="<?= isset($errors['duree']) ? 'input-error' : '' ?>">
                    <?php if (isset($errors['duree'])): ?>
                        <span class="form-error"><?= htmlspecialchars($errors['duree']) ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group">
                <label for="synopsis">Synopsis <span class="required-mark">*</span></label>
                <textarea id="synopsis" name="synopsis" rows="6"
                    placeholder="Le heros commence son periple..."
                    class="<?= isset($errors['synopsis']) ? 'input-error' : '' ?>"><?= htmlspecialchars($synopsis) ?></textarea>
                <?php if (isset($errors['synopsis'])): ?>
                    <span class="form-error"><?= htmlspecialchars($errors['synopsis']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="image">Affiche web (URL de l'image) <span class="required-mark">*</span></label>
                <input type="text" id="image" name="image" value="<?= htmlspecialchars($image) ?>"
                    placeholder="https://exemple.com/image.jpg"
                    class="<?= isset($errors['image']) ? 'input-error' : '' ?>">
                <?php if (isset($errors['image'])): ?>
                    <span class="form-error"><?= htmlspecialchars($errors['image']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="id_genre">Genre <span class="required-mark">*</span></label>
                    <select id="id_genre" name="id_genre"
                        class="<?= isset($errors['id_genre']) ? 'input-error' : '' ?>">
                        <option value="">Sélectionnez un genre...</option>
                        <?php foreach ($genres as $genre): ?>
                            <option value="<?= htmlspecialchars((string) $genre['id']) ?>"
                                <?= $id_genre === (string) $genre['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($genre['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['id_genre'])): ?>
                        <span class="form-error"><?= htmlspecialchars($errors['id_genre']) ?></span>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="id_pays">Pays <span class="required-mark">*</span></label>
                    <select id="id_pays" name="id_pays"
                        class="<?= isset($errors['id_pays']) ? 'input-error' : '' ?>">
                        <option value="">Sélectionnez un pays...</option>
                        <?php foreach ($paysListe as $pays): ?>
                            <option value="<?= htmlspecialchars((string) $pays['id']) ?>"
                                <?= $id_pays === (string) $pays['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($pays['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['id_pays'])): ?>
                        <span class="form-error"><?= htmlspecialchars($errors['id_pays']) ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <button type="submit" class="btn-primary btn-submit-film">
                <i data-lucide="plus-circle"></i> Ajouter ce film au catalogue
            </button>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../src/includes/footer.php'; ?>