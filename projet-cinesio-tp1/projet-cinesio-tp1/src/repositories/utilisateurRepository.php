<?php

function findUtilisateurByEmail(string $email): array|false
{
    require_once __DIR__ . '/../database/connection.php';
    $connexion = connectDatabase();

    $requeteSql = "SELECT id, email, pseudo, mot_de_passe FROM utilisateur WHERE email = :email";

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

    $requeteSql = "SELECT id, email, pseudo, mot_de_passe FROM utilisateur WHERE pseudo = :pseudo";

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

    $requeteSql = "INSERT INTO utilisateur (email, pseudo, mot_de_passe) VALUES (:email, :pseudo, :mot_de_passe)";

    $requete = $connexion->prepare($requeteSql);

    return $requete->execute([
        ':email' => $data['email'],
        ':pseudo' => $data['pseudo'],
        ':mot_de_passe' => password_hash($data['mot_de_passe'], PASSWORD_DEFAULT),
    ]);
}