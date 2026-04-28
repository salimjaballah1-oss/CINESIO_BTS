<?php
//findUtilisateurByEmail(string $email) : pour vérifier si un email est déjà utilisé.
function findUtilisateurByEmail(string $email): array
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
function findUtilisateurByPseudo(string $pseudo): array
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
