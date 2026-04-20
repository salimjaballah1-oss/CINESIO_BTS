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

function findAllGenres(): array
{
    require_once __DIR__ . '/../database/connection.php';
    $connexion = connectDatabase();

    $requeteSql = "SELECT id, nom
    FROM genre
    ORDER BY nom";

    $requete = $connexion->prepare($requeteSql);
    $requete->execute();

    $resultat = $requete->fetchAll(PDO::FETCH_ASSOC);
    return $resultat;
}

function findAllPays(): array
{
    require_once __DIR__ . '/../database/connection.php';
    $connexion = connectDatabase();

    $requeteSql = "SELECT id, nom
    FROM pays
    ORDER BY nom";

    $requete = $connexion->prepare($requeteSql);
    $requete->execute();

    $resultat = $requete->fetchAll(PDO::FETCH_ASSOC);
    return $resultat;
}

function insertFilm(array $filmData): bool
{
    require_once __DIR__ . '/../database/connection.php';
    $connexion = connectDatabase();

    $requeteSql = "INSERT INTO film (titre, date_sortie, duree, synopsis, image, id_genre, id_pays)
    VALUES (:titre, :date_sortie, :duree, :synopsis, :image, :id_genre, :id_pays)";

    $requete = $connexion->prepare($requeteSql);

    return $requete->execute([
        ':titre' => $filmData['titre'],
        ':date_sortie' => $filmData['date_sortie'],
        ':duree' => $filmData['duree'],
        ':synopsis' => $filmData['synopsis'],
        ':image' => $filmData['image'],
        ':id_genre' => $filmData['id_genre'],
        ':id_pays' => $filmData['id_pays'],
    ]);
}
