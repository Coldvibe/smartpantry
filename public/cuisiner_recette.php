<?php
require_once "../backend/classes/Database.php";
require_once "../backend/classes/Recette.php";
require_once "../backend/classes/Ingredient.php";
require_once "../backend/classes/Produit.php";

$pdo = Database::getInstance()->getConnection();
$recetteObj = new Recette($pdo);
$ingredientObj = new Ingredient();
$produitObj = new Produit();

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $recette_id = intval($_GET['id']);

    // Vérifier si la recette est réalisable
    if ($ingredientObj->recetteRealisable($recette_id)) {
        $ingredients = $ingredientObj->getIngredientsByRecette($recette_id);

        // Déduire les quantités des ingrédients du stock
        foreach ($ingredients as $ing) {
            $produitObj->deduireStock($ing['name'], $ing['quantity']);
        }
        
        // Redirection avec message de succès
        header("Location: recettes.php?success=Recette cuisinée avec succès !");
        exit();
    } else {
        // Redirection avec message d'erreur
        header("Location: recettes.php?error=Impossible de cuisiner : ingrédients manquants !");
        exit();
    }
} else {
    header("Location: recettes.php?error=ID de recette invalide !");
    exit();
}
?>
