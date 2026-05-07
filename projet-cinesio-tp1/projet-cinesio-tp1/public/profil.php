<?php
require_once __DIR__ . '/../src/includes/header.php';
require_once __DIR__ . '/../src/repositories/utilisateurRepository.php';

if (!isset($_SESSION['utilisateur'])) {
    header('Location: connexion.php');
    exit;
}

$erreurs = [];
$succes = false;

$utilisateurId = (int) $_SESSION['utilisateur']['id'];
$utilisateur = findUtilisateurById($utilisateurId);
if (!$utilisateur) {
    session_destroy();
    header('Location: connexion.php');
    exit;
}

function isAllowedImageMime(string $mime): bool
{
    return in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true);
}

function detectImageMime(string $tmpPath): string
{
    if (class_exists('finfo')) {
        try {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($tmpPath);
            if (is_string($mime) && $mime !== '') {
                return $mime;
            }
        } catch (Throwable) {
            // ignore, try other methods
        }
    }

    if (function_exists('mime_content_type')) {
        $mime = @mime_content_type($tmpPath);
        if (is_string($mime) && $mime !== '') {
            return $mime;
        }
    }

    if (function_exists('getimagesize')) {
        $info = @getimagesize($tmpPath);
        if (is_array($info) && isset($info['mime']) && is_string($info['mime'])) {
            return $info['mime'];
        }
    }

    return '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['photo_profil']) || !is_array($_FILES['photo_profil'])) {
        $erreurs['photo_profil'] = "Veuillez sélectionner une image.";
    } else {
        $file = $_FILES['photo_profil'];

        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            $erreurs['photo_profil'] = "Une erreur est survenue lors de l'upload.";
        } else {
            $maxBytes = 2 * 1024 * 1024;
            if (($file['size'] ?? 0) > $maxBytes) {
                $erreurs['photo_profil'] = "L'image est trop lourde (2 Mo max).";
            } else {
                $tmpPath = (string) ($file['tmp_name'] ?? '');
                $mime = detectImageMime($tmpPath);

                if (!isAllowedImageMime($mime)) {
                    $erreurs['photo_profil'] = "Format non supporté (JPG, PNG ou WebP).";
                } else {
                    $ext = match ($mime) {
                        'image/jpeg' => 'jpg',
                        'image/png' => 'png',
                        'image/webp' => 'webp',
                        default => 'img',
                    };

                    $uploadDir = __DIR__ . '/uploads/avatars';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }

                    $fileName = 'user_' . $utilisateurId . '_' . time() . '.' . $ext;
                    $destPath = $uploadDir . '/' . $fileName;

                    if (!move_uploaded_file($tmpPath, $destPath)) {
                        $erreurs['photo_profil'] = "Impossible d'enregistrer l'image.";
                    } else {
                        $publicPath = 'uploads/avatars/' . $fileName;

                        // Supprime l'ancienne image (si elle est locale et dans uploads/avatars)
                        $ancienne = (string) ($utilisateur['photo_profil'] ?? '');
                        if (str_starts_with($ancienne, 'uploads/avatars/')) {
                            $oldDiskPath = __DIR__ . '/' . $ancienne;
                            if (is_file($oldDiskPath)) {
                                @unlink($oldDiskPath);
                            }
                        }

                        if (updateUtilisateurPhotoProfil($utilisateurId, $publicPath)) {
                            $_SESSION['utilisateur']['photo_profil'] = $publicPath;
                            $succes = true;
                            $utilisateur['photo_profil'] = $publicPath;
                        } else {
                            $erreurs['photo_profil'] = "Impossible de sauvegarder la photo de profil.";
                        }
                    }
                }
            }
        }
    }
}
?>

<div class="container profil-container">
    <div class="inscription-header">
        <h1>Mon profil</h1>
        <p class="inscription-subtitle">Gérez votre photo de profil.</p>
    </div>

    <div class="inscription-card">
        <?php if ($succes): ?>
            <div class="alert alert-success" role="alert">
                <strong>OK.</strong> Votre photo de profil a été mise à jour.
            </div>
        <?php endif; ?>

        <div class="profil-avatar-row">
            <?php if (!empty($utilisateur['photo_profil'])): ?>
                <img class="profil-avatar" src="<?= htmlspecialchars($utilisateur['photo_profil']) ?>" alt="Photo de profil">
            <?php else: ?>
                <div class="profil-avatar profil-avatar--placeholder">
                    <i data-lucide="user" class="profil-avatar-icon"></i>
                </div>
            <?php endif; ?>
            <div class="profil-meta">
                <div class="profil-pseudo"><?= htmlspecialchars($utilisateur['pseudo'] ?? $_SESSION['utilisateur']['pseudo']) ?></div>
                <div class="profil-email"><?= htmlspecialchars($utilisateur['email'] ?? '') ?></div>
            </div>
        </div>

        <form method="POST" action="" enctype="multipart/form-data" novalidate>
            <div class="form-row">
                <div class="form-group form-group-full">
                    <label for="photo_profil" class="form-label">Nouvelle photo de profil</label>
                    <input type="file"
                           class="form-control <?= isset($erreurs['photo_profil']) ? 'input-error' : '' ?>"
                           id="photo_profil"
                           name="photo_profil"
                           accept="image/png,image/jpeg,image/webp"
                           required>
                    <?php if (isset($erreurs['photo_profil'])): ?>
                        <span class="form-error"><?= htmlspecialchars($erreurs['photo_profil']) ?></span>
                    <?php else: ?>
                        <small class="form-text">JPG/PNG/WebP, 2 Mo maximum.</small>
                    <?php endif; ?>
                </div>
            </div>

            <button type="submit" class="btn btn-inscribe">
                <i data-lucide="upload" class="btn-icon"></i>
                Mettre à jour
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../src/includes/footer.php'; ?>

