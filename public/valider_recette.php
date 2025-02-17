<?php
require_once '../backend/classes/Database.php';
require_once '../backend/classes/Recette.php';
require_once '../backend/classes/Produit.php';

// Récupération de la connexion via la classe Database
$pdo = Database::getConnection();
$recette = new Recette($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recette_id']) && is_numeric($_POST['recette_id'])) {
    $recette_id = $_POST['recette_id'];

    // Exécution de la validation de la recette
    $result = $recette->validerRecette($recette_id);

    if ($result) {
        header("Location: details_recette.php?id=" . $recette_id . "&success=1");
        exit();
    } else {
        header("Location: details_recette.php?id=" . $recette_id . "&error=1");
        exit();
    }
} else {
    header("Location: recettes.php?error=invalid_id");
    exit();
}
?>
