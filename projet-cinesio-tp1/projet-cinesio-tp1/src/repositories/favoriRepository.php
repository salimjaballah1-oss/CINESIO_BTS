<?php

function ensureFavoriTable(PDO $connexion): void
{
    static $alreadyEnsured = false;
    if ($alreadyEnsured) {
        return;
    }
    $alreadyEnsured = true;

    try {
        $connexion->exec(
            "CREATE TABLE IF NOT EXISTS favori (
                utilisateur_id INTEGER NOT NULL,
                film_id INTEGER NOT NULL,
                created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
                PRIMARY KEY (utilisateur_id, film_id)
            )"
        );

        // Index utile pour l'affichage des favoris
        $connexion->exec("CREATE INDEX IF NOT EXISTS idx_favori_utilisateur_id ON favori (utilisateur_id)");
    } catch (Throwable) {
        // Si on n'a pas les droits, on n'empêche pas le reste du site de fonctionner.
    }
}

function isFilmFavori(int $utilisateurId, int $filmId): bool
{
    require_once __DIR__ . '/../database/connection.php';
    $connexion = connectDatabase();
    ensureFavoriTable($connexion);

    $sql = "SELECT 1 FROM favori WHERE utilisateur_id = :uid AND film_id = :fid";
    $req = $connexion->prepare($sql);
    $req->execute([':uid' => $utilisateurId, ':fid' => $filmId]);

    return (bool) $req->fetchColumn();
}

function addFavori(int $utilisateurId, int $filmId): bool
{
    require_once __DIR__ . '/../database/connection.php';
    $connexion = connectDatabase();
    ensureFavoriTable($connexion);

    $sql = "INSERT INTO favori (utilisateur_id, film_id) VALUES (:uid, :fid)
            ON CONFLICT (utilisateur_id, film_id) DO NOTHING";
    $req = $connexion->prepare($sql);
    return $req->execute([':uid' => $utilisateurId, ':fid' => $filmId]);
}

function removeFavori(int $utilisateurId, int $filmId): bool
{
    require_once __DIR__ . '/../database/connection.php';
    $connexion = connectDatabase();
    ensureFavoriTable($connexion);

    $sql = "DELETE FROM favori WHERE utilisateur_id = :uid AND film_id = :fid";
    $req = $connexion->prepare($sql);
    return $req->execute([':uid' => $utilisateurId, ':fid' => $filmId]);
}

function findFavorisFilmsByUtilisateurId(int $utilisateurId): array
{
    require_once __DIR__ . '/../database/connection.php';
    $connexion = connectDatabase();
    ensureFavoriTable($connexion);

    $sql = "SELECT film.id, film.titre, genre.nom AS genre, film.duree, film.synopsis, film.image, pays.nom AS pays
            FROM favori
            JOIN film ON film.id = favori.film_id
            JOIN genre ON film.id_genre = genre.id
            JOIN pays ON film.id_pays = pays.id
            WHERE favori.utilisateur_id = :uid
            ORDER BY favori.created_at DESC";
    $req = $connexion->prepare($sql);
    $req->execute([':uid' => $utilisateurId]);

    return $req->fetchAll(PDO::FETCH_ASSOC) ?: [];
}

