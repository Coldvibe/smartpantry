<?php
require_once "../backend/classes/Database.php";
require_once "../backend/classes/Recette.php";
require_once "../backend/classes/Ingredient.php";

$pdo = Database::getInstance()->getConnection();
$recetteObj = new Recette($pdo);
$ingredientObj = new Ingredient();

// Vérification de l'ID et suppression de la recette
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $recette_id = intval($_GET['id']);

    // Supprimer les ingrédients liés à la recette
    $ingredientObj->supprimerIngredientsParRecette($recette_id);

    // Supprimer la recette
    $recetteObj->supprimerRecette($recette_id);

    // Redirection avec message de succès
    header("Location: recettes.php?success=Recette supprimée !");
    exit();
} else {
    // Redirection en cas d'ID invalide
    header("Location: recettes.php?error=ID de recette invalide !");
    exit();
}
?>
