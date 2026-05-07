<?php
require_once __DIR__ . '/../src/repositories/favoriRepository.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['utilisateur'])) {
    header('Location: connexion.php');
    exit;
}

$filmId = $_POST['film_id'] ?? '';
$redirect = $_POST['redirect'] ?? 'index.php';

if (filter_var($filmId, FILTER_VALIDATE_INT) === false || (int) $filmId <= 0) {
    header('Location: ' . $redirect);
    exit;
}

$filmId = (int) $filmId;
$utilisateurId = (int) $_SESSION['utilisateur']['id'];

if (isFilmFavori($utilisateurId, $filmId)) {
    removeFavori($utilisateurId, $filmId);
} else {
    addFavori($utilisateurId, $filmId);
}

header('Location: ' . $redirect);
exit;

