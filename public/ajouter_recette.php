<?php
require_once "../backend/classes/Recette.php";
require_once "../backend/classes/Ingredient.php";

$recette = new Recette();
$ingredient = new Ingredient();

// Ajout d’une recette
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_recette"])) {
    $nom = $_POST["name"];
    $description = $_POST["description"];
    $portions = $_POST["portions"];
    $preparation_time = $_POST["preparation_time"];
    $cooking_time = $_POST["cooking_time"];

    $recette->ajouterRecette($nom, $description, $portions, $preparation_time, $cooking_time);
    
    // Redirection vers la liste des recettes
    header("Location: ajouter_recette.php?success=1");
    exit();
}

// Ajout d’un ingrédient à une recette
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_ingredient"])) {
    $recette_id = $_POST["recette_id"];
    $ingredient_name = $_POST["ingredient_name"];
    $quantity = $_POST["quantity"];
    $unit = $_POST["unit"];

    $ingredient->ajouterIngredient($recette_id, $ingredient_name, $quantity, $unit);
}

// Récupération de toutes les recettes
$recettes = $recette->getRecettes();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une Recette - SmartPantry</title>
    <link rel="stylesheet" href="style.css">
    <!-- Ajout de Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</head>
<body>
    <h1>Ajouter une Recette</h1>

    <form method="POST">
        <input type="text" name="name" placeholder="Nom de la recette" required>
        <textarea name="description" placeholder="Description"></textarea>
        <input type="number" name="portions" placeholder="Nombre de portions" required>
        <input type="number" name="preparation_time" placeholder="Temps de préparation (min)" required>
        <input type="number" name="cooking_time" placeholder="Temps de cuisson (min)" required>
        <button type="submit" name="add_recette">Ajouter</button>
    </form>

    <h2>Ajouter des ingrédients</h2>
    <form method="POST">
        <select name="recette_id" required>
            <option value="">Sélectionnez une recette</option>
            <?php foreach ($recettes as $r) : ?>
                <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <input type="text" name="ingredient_name" placeholder="Nom de l'ingrédient" required>
        <input type="number" name="quantity" placeholder="Quantité" required>
        <input type="text" name="unit" placeholder="Unité (g, ml, pièces...)" required>
        <button type="submit" name="add_ingredient">Ajouter</button>
    </form>

    <a href="recettes.php">⬅️ Retour aux Recettes</a>
</body>
</html>
