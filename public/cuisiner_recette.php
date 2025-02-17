<?php
require_once "../backend/classes/Recette.php";
require_once "../backend/classes/Ingredient.php";
require_once "../backend/classes/Produit.php";

if (isset($_GET['id'])) {
    $recette_id = $_GET['id'];
    $ingredientObj = new Ingredient();
    $produitObj = new Produit();

    // Vérifier si la recette est réalisable
    if ($ingredientObj->recetteRealisable($recette_id)) {
        $ingredients = $ingredientObj->getIngredientsByRecette($recette_id);

        foreach ($ingredients as $ing) {
            $produitObj->deduireStock($ing['name'], $ing['quantity']);
        }
        echo "<script>alert('Recette cuisinée avec succès !'); window.location='recettes.php';</script>";
    } else {
        echo "<script>alert('Impossible de cuisiner : ingrédients manquants !'); window.location='recettes.php';</script>";
    }
}
