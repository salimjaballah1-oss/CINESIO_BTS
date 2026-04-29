<?php
//findUtilisateurByEmail(string $email) : pour vérifier si un email est déjà utilisé.
function findUtilisateurByEmail(string $email): array|false
{
    require_once __DIR__ . '/../database/connection.php';
    $connexion = connectDatabase();

    $requeteSql = "SELECT id, nom, prenom, email, mot_de_passe FROM utilisateur WHERE email = :email";

    $requete = $connexion->prepare($requeteSql);
    $requete->bindParam(':email', $email, PDO::PARAM_STR);
    $requete->execute();

    $resultat = $requete->fetch(PDO::FETCH_ASSOC);
    return $resultat;
}

//findUtilisateurByPseudo(string $pseudo) : pour vérifier si un pseudo est déjà utilisé.
function findUtilisateurByPseudo(string $pseudo): array|false
{
    require_once __DIR__ . '/../database/connection.php';
    $connexion = connectDatabase();

    $requeteSql = "SELECT id, nom, prenom, email, mot_de_passe FROM utilisateur WHERE pseudo = :pseudo";

    $requete = $connexion->prepare($requeteSql);
    $requete->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
    $requete->execute();

    $resultat = $requete->fetch(PDO::FETCH_ASSOC);
    return $resultat;
}

//createUtilisateur(array $data) : pour insérer les données du nouvel utilisateur avec une requête préparée (INSERT INTO utilisateur...).
function createUtilisateur(array $data): bool
{
    require_once __DIR__ . '/../database/connection.php';
    $connexion = connectDatabase();

    $requeteSql = "INSERT INTO utilisateur (nom, prenom, email, pseudo, mot_de_passe) VALUES (:nom, :prenom, :email, :pseudo, :mot_de_passe)";

    $requete = $connexion->prepare($requeteSql);

    return $requete->execute([
        ':nom' => $data['nom'],
        ':prenom' => $data['prenom'],
        ':email' => $data['email'],
        ':pseudo' => $data['pseudo'],
        ':mot_de_passe' => password_hash($data['mot_de_passe'], PASSWORD_DEFAULT),
    ]);
}