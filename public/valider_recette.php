<?php
require_once '../backend/classes/Database.php';
require_once '../backend/classes/Recette.php';
require_once '../backend/classes/Produit.php';

// Vérification de la connexion
$pdo = Database::getInstance()->getConnection();
$recette = new Recette($pdo);
$produit = new Produit($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recette_id']) && is_numeric($_POST['recette_id'])) {
    $recette_id = $_POST['recette_id'];

    // Vérifier si la recette est réalisable
    if (!$recette->recetteRealisable($recette_id)) {
        header("Location: details_recette.php?id=" . $recette_id . "&error=stock_insuffisant");
        exit();
    }

    // Exécution de la validation de la recette
    $result = $recette->validerRecette($recette_id);

    if ($result) {
        header("Location: details_recette.php?id=" . $recette_id . "&success=recette_validee");
        exit();
    } else {
        header("Location: details_recette.php?id=" . $recette_id . "&error=erreur_validation");
        exit();
    }
} else {
    header("Location: recettes.php?error=invalid_id");
    exit();
}
?>
