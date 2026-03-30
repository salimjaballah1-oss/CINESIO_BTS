<?php
function convertirDuree(int $minutes): string {
    if ($minutes < 60) {
        return $minutes . " min";
    }
    $heures = floor($minutes / 60);
    $restesMinutes = $minutes % 60;

    // Si rond
    if ($restesMinutes == 0) {
        return $heures . "h";
    }

    // Pad avec un 0 si besoin (ex: 2h 05min)
    $restesMinutes = str_pad($restesMinutes, 2, "0", STR_PAD_LEFT);
    return $heures . "h " . $restesMinutes . "min";
}

function getCountryCode(string $country): string {
    $map = [
        "USA" => "USA",
        "Corée du Sud" => "COR",
        "France" => "FRA",
        "Japon" => "JAP"
    ];
    return isset($map[$country]) ? $map[$country] : strtoupper(substr($country, 0, 3));
}
