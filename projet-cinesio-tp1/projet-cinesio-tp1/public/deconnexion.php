<?php
session_start();
unset($_SESSION['utilisateur']);
session_destroy(); // Optionnel mais conseillé pour tout nettoyer
header('Location: index.php');
exit;
