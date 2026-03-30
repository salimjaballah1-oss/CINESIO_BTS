<?php
function findAllFilms(): array
{
    require_once __DIR__ . '/../database/connection.php';
    $connexion = connectDatabase();

    $requeteSql = "SELECT film.id, film.titre, genre.nom AS genre, film.duree, film.synopsis, film.image, pays.nom AS pays
    FROM film, genre, pays
    WHERE film.id_genre = genre.id
    AND film.id_pays = pays.id
    ORDER BY film.titre";

    $requete = $connexion->prepare($requeteSql);
    $requete->execute();

    $resultat = $requete->fetchAll(PDO::FETCH_ASSOC);
    return $resultat;
}

function findFilmById(int $id): array|false
{
    require_once __DIR__ . '/../database/connection.php';
    $connexion = connectDatabase();

    $requeteSql = "SELECT film.id, film.titre, film.date_sortie, genre.nom AS genre, film.duree, film.synopsis, film.image, pays.nom AS pays
    FROM film, genre, pays
    WHERE film.id_genre = genre.id
    AND film.id_pays = pays.id
    AND film.id = :id";

    $requete = $connexion->prepare($requeteSql);
    $requete->bindParam(':id', $id, PDO::PARAM_INT);
    $requete->execute();

    $resultat = $requete->fetch(PDO::FETCH_ASSOC);
    return $resultat;
}
