<?php
require_once __DIR__ . '/../src/includes/header.php';
require_once __DIR__ . '/../src/repositories/utilisateurRepository.php';

$erreurs = [];
$succes = false;
$email = '';
$pseudo = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pseudo = trim($_POST['pseudo'] ?? '');
    $motDePasse = $_POST['mot_de_passe'] ?? '';
    $confirmationMotDePasse = $_POST['confirmation_mot_de_passe'] ?? '';

    // Validation de l'email
    if (empty($email)) {
        $erreurs['email'] = "L'email est obligatoire.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs['email'] = "Le format de l'email n'est pas valide.";
    } elseif (findUtilisateurByEmail($email)) {
        $erreurs['email'] = "Cet email est déjà utilisé.";
    }

    // Validation du pseudo
    if (empty($pseudo)) {
        $erreurs['pseudo'] = "Le pseudo est obligatoire.";
    } elseif (strlen($pseudo) < 3) {
        $erreurs['pseudo'] = "Le pseudo doit contenir au moins 3 caractères.";
    } elseif (findUtilisateurByPseudo($pseudo)) {
        $erreurs['pseudo'] = "Ce pseudo est déjà utilisé.";
    }

    // Validation du mot de passe
    if (empty($motDePasse)) {
        $erreurs['mot_de_passe'] = "Le mot de passe est obligatoire.";
    } elseif (strlen($motDePasse) < 8) {
        $erreurs['mot_de_passe'] = "Le mot de passe doit contenir au moins 8 caractères.";
    }

    // Validation de la confirmation du mot de passe
    if (empty($confirmationMotDePasse)) {
        $erreurs['confirmation_mot_de_passe'] = "Vous devez confirmer le mot de passe.";
    } elseif ($motDePasse !== $confirmationMotDePasse) {
        $erreurs['confirmation_mot_de_passe'] = "Les mots de passe ne correspondent pas.";
    }


    if (empty($erreurs)) {
        $data = [
            'email' => $email,
            'pseudo' => $pseudo,
            'mot_de_passe' => $motDePasse
        ];

        if (createUtilisateur($data)) {
            $succes = true;
            $email = '';
            $pseudo = '';
            $motDePasse = '';
            $confirmationMotDePasse = '';
        } else {
            $erreurs['general'] = "Une erreur est survenue lors de l'inscription. Veuillez réessayer.";
        }
    }
}
?>

<div class="container inscription-container">
    <div class="inscription-header">
        <h1>Créer un compte</h1>
        <p class="inscription-subtitle">Rejoignez la communauté CinéSIO pour accéder à toutes les fonctionnalités.</p>
    </div>

    <div class="inscription-card">
        <?php if ($succes): ?>
            <div class="alert alert-success" role="alert">
                <strong>Succès !</strong> Inscription réussie ! Vous pouvez maintenant vous connecter.
            </div>
        <?php endif; ?>

        <?php if (isset($erreurs['general'])): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($erreurs['general']) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-row">
                <div class="form-group form-group-full">
                    <label for="email" class="form-label">Adresse Email <span class="required">*</span></label>
                    <input type="email" class="form-control <?= isset($erreurs['email']) ? 'input-error' : '' ?>" id="email" name="email" required
                        placeholder="Ex: jean.dupont@email.com" value="<?php echo htmlspecialchars($email); ?>">
                    <?php if (isset($erreurs['email'])): ?>
                        <span class="form-error"><?= htmlspecialchars($erreurs['email']) ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group form-group-full">
                    <label for="pseudo" class="form-label">Pseudonyme <span class="required">*</span></label>
                    <input type="text" class="form-control <?= isset($erreurs['pseudo']) ? 'input-error' : '' ?>" id="pseudo" name="pseudo" required minlength="3"
                        placeholder="Ex: JeanD88" value="<?php echo htmlspecialchars($pseudo); ?>">
                    <?php if (isset($erreurs['pseudo'])): ?>
                        <span class="form-error"><?= htmlspecialchars($erreurs['pseudo']) ?></span>
                    <?php else: ?>
                        <small class="form-text">3 caractères minimum.</small>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-row two-columns">
                <div class="form-group">
                    <label for="mot_de_passe" class="form-label">Mot de passe <span class="required">*</span></label>
                    <input type="password" class="form-control <?= isset($erreurs['mot_de_passe']) ? 'input-error' : '' ?>" id="mot_de_passe" name="mot_de_passe" required
                        minlength="8">
                    <?php if (isset($erreurs['mot_de_passe'])): ?>
                        <span class="form-error"><?= htmlspecialchars($erreurs['mot_de_passe']) ?></span>
                    <?php else: ?>
                        <small class="form-text">8 caractères minimum.</small>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="confirmation_mot_de_passe" class="form-label">Confirmation <span
                            class="required">*</span></label>
                    <input type="password" class="form-control <?= isset($erreurs['confirmation_mot_de_passe']) ? 'input-error' : '' ?>" id="confirmation_mot_de_passe"
                        name="confirmation_mot_de_passe" required minlength="8">
                    <?php if (isset($erreurs['confirmation_mot_de_passe'])): ?>
                        <span class="form-error"><?= htmlspecialchars($erreurs['confirmation_mot_de_passe']) ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <button type="submit" class="btn btn-inscribe">
                <i data-lucide="user-plus" class="btn-icon"></i>
                M'inscrire maintenant
            </button>
        </form>

        <div class="inscription-footer">
            <p>Déjà un compte ? <a href="connexion.php">Connectez-vous</a></p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../src/includes/footer.php'; ?>