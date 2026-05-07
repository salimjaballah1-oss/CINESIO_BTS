<?php

function ensureUtilisateurPhotoProfilColumn(PDO $connexion): void
{
    static $alreadyEnsured = false;
    if ($alreadyEnsured) {
        return;
    }

    $alreadyEnsured = true;

    try {
        $connexion->exec("ALTER TABLE utilisateur ADD COLUMN IF NOT EXISTS photo_profil TEXT");
    } catch (Throwable) {
        // Si on n'a pas les droits ou autre, on n'empêche pas le reste du site de fonctionner.
    }
}

function findUtilisateurByEmail(string $email): array|false
{
    require_once __DIR__ . '/../database/connection.php';
    $connexion = connectDatabase();
    ensureUtilisateurPhotoProfilColumn($connexion);

    $requeteSql = "SELECT id, email, pseudo, mot_de_passe, photo_profil FROM utilisateur WHERE email = :email";

    $requete = $connexion->prepare($requeteSql);
    $requete->bindParam(':email', $email, PDO::PARAM_STR);
    $requete->execute();

    $resultat = $requete->fetch(PDO::FETCH_ASSOC);
    return $resultat;
}


function findUtilisateurByPseudo(string $pseudo): array|false
{
    require_once __DIR__ . '/../database/connection.php';
    $connexion = connectDatabase();
    ensureUtilisateurPhotoProfilColumn($connexion);

    $requeteSql = "SELECT id, email, pseudo, mot_de_passe, photo_profil FROM utilisateur WHERE pseudo = :pseudo";

    $requete = $connexion->prepare($requeteSql);
    $requete->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
    $requete->execute();

    $resultat = $requete->fetch(PDO::FETCH_ASSOC);
    return $resultat;
}


function createUtilisateur(array $data): bool
{
    require_once __DIR__ . '/../database/connection.php';
    $connexion = connectDatabase();
    ensureUtilisateurPhotoProfilColumn($connexion);

    $requeteSql = "INSERT INTO utilisateur (email, pseudo, mot_de_passe) VALUES (:email, :pseudo, :mot_de_passe)";

    $requete = $connexion->prepare($requeteSql);

    return $requete->execute([
        ':email' => $data['email'],
        ':pseudo' => $data['pseudo'],
        ':mot_de_passe' => password_hash($data['mot_de_passe'], PASSWORD_DEFAULT),
    ]);
}

function findUtilisateurById(int $id): array|false
{
    require_once __DIR__ . '/../database/connection.php';
    $connexion = connectDatabase();
    ensureUtilisateurPhotoProfilColumn($connexion);

    $requeteSql = "SELECT id, email, pseudo, photo_profil FROM utilisateur WHERE id = :id";
    $requete = $connexion->prepare($requeteSql);
    $requete->bindParam(':id', $id, PDO::PARAM_INT);
    $requete->execute();

    return $requete->fetch(PDO::FETCH_ASSOC);
}

function updateUtilisateurPhotoProfil(int $id, ?string $photoProfil): bool
{
    require_once __DIR__ . '/../database/connection.php';
    $connexion = connectDatabase();
    ensureUtilisateurPhotoProfilColumn($connexion);

    $requeteSql = "UPDATE utilisateur SET photo_profil = :photo_profil WHERE id = :id";
    $requete = $connexion->prepare($requeteSql);

    return $requete->execute([
        ':id' => $id,
        ':photo_profil' => $photoProfil,
    ]);
}