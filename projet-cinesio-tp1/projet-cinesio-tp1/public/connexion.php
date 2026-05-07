<?php
require_once __DIR__ . '/../src/includes/header.php';
require_once __DIR__ . '/../src/repositories/utilisateurRepository.php';

$erreurs = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $motDePasse = $_POST['mot_de_passe'] ?? '';

    if (empty($email)) {
        $erreurs[] = "L'email est obligatoire.";
    }
    if (empty($motDePasse)) {
        $erreurs[] = "Le mot de passe est obligatoire.";
    }

    if (empty($erreurs)) {
        $utilisateur = findUtilisateurByEmail($email);

        if ($utilisateur && password_verify($motDePasse, $utilisateur['mot_de_passe'])) {
            // Démarrer la session si ce n'est pas déjà fait (normalement fait dans header.php)
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
            $erreurs[] = "Identifiants invalides.";
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
        <?php if (!empty($erreurs)): ?>
            <div class="alert alert-danger" role="alert">
                <strong>Erreur :</strong>
                <ul class="mb-0 mt-2">
                    <?php foreach ($erreurs as $erreur): ?>
                        <li><?php echo htmlspecialchars($erreur); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-row">
                <div class="form-group form-group-full">
                    <label for="email" class="form-label">Adresse Email</label>
                    <input type="email" class="form-control" id="email" name="email" required
                        placeholder="votre@email.com" value="<?php echo htmlspecialchars($email); ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group form-group-full">
                    <label for="mot_de_passe" class="form-label">Mot de passe</label>
                    <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" required>
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
