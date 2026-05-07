<?php
require_once __DIR__ . '/../src/includes/header.php';
require_once __DIR__ . '/../src/repositories/utilisateurRepository.php';

$erreurs = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $motDePasse = $_POST['mot_de_passe'] ?? '';

    if (empty($email)) {
        $erreurs['email'] = "L'email est obligatoire.";
    }
    if (empty($motDePasse)) {
        $erreurs['mot_de_passe'] = "Le mot de passe est obligatoire.";
    }

    if (empty($erreurs)) {
        $utilisateur = findUtilisateurByEmail($email);

        if ($utilisateur && password_verify($motDePasse, $utilisateur['mot_de_passe'])) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            $_SESSION['utilisateur'] = [
                'id' => $utilisateur['id'],
                'pseudo' => $utilisateur['pseudo']
            ];

            header('Location: index.php');
            exit;
        } else {
            $erreurs['general'] = "Identifiants invalides.";
        }
    }
}
?>

<div class="container inscription-container">
    <div class="inscription-header">
        <h1>Connexion</h1>
        <p class="inscription-subtitle">Accédez à votre espace membre CinéSIO.</p>
    </div>

    <div class="inscription-card">
        <?php if (isset($erreurs['general'])): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($erreurs['general']) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" novalidate>
            <div class="form-row">
                <div class="form-group form-group-full">
                    <label for="email" class="form-label">Adresse Email</label>
                    <input type="email" class="form-control <?= isset($erreurs['email']) ? 'input-error' : '' ?>" id="email" name="email" required
                        placeholder="votre@email.com" value="<?php echo htmlspecialchars($email); ?>">
                    <?php if (isset($erreurs['email'])): ?>
                        <span class="form-error"><?= htmlspecialchars($erreurs['email']) ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group form-group-full">
                    <label for="mot_de_passe" class="form-label">Mot de passe</label>
                    <input type="password" class="form-control <?= isset($erreurs['mot_de_passe']) ? 'input-error' : '' ?>" id="mot_de_passe" name="mot_de_passe" required>
                    <?php if (isset($erreurs['mot_de_passe'])): ?>
                        <span class="form-error"><?= htmlspecialchars($erreurs['mot_de_passe']) ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <button type="submit" class="btn btn-inscribe">
                <i data-lucide="log-in" class="btn-icon"></i>
                Se connecter
            </button>
        </form>

        <div class="inscription-footer">
            <p>Pas encore de compte ? <a href="inscription.php">Créer un compte</a></p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../src/includes/footer.php'; ?>
