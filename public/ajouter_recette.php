<?php
require_once "../backend/classes/Database.php";
require_once "../backend/classes/Recette.php";
require_once "../backend/classes/Ingredient.php";

$pdo = Database::getInstance()->getConnection();
$recetteObj = new Recette($pdo);
$ingredientObj = new Ingredient();

// 📌 Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_recette"])) {
    $nom = $_POST["name"];
    $description = $_POST["description"];
    $portions = (int) $_POST["portions"];
    $preparation_time = (int) $_POST["preparation_time"];
    $cooking_time = (int) $_POST["cooking_time"];
    $ingredients = $_POST["ingredients"];
    
    // 📷 Gestion de l'upload de l'image
    $image = "default.jpg"; 
    if (!empty($_FILES["image"]["name"])) {
        $image = basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], "assets/images/recettes/" . $image);
    }

    // 📌 Ajouter la recette et récupérer son ID
    $recette_id = $recetteObj->ajouterRecette($nom, $description, $portions, $preparation_time, $cooking_time, $image);

    if ($recette_id) {
        // 📌 Ajouter les ingrédients liés à la recette
        foreach ($ingredients as $ing) {
            $ingredientObj->ajouterIngredient($recette_id, $ing["name"], $ing["quantity"], $ing["unit"]);
        }

        header("Location: recettes.php?success=Recette ajoutée avec succès !");
        exit();
    } else {
        header("Location: ajouter_recette.php?error=Erreur lors de l'ajout !");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une Recette - SmartPantry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container mt-5">
    <h2 class="page-title">➕ Ajouter une Recette</h2>
    <form method="POST" action="" enctype="multipart/form-data" class="form-container">
        <div class="mb-3">
            <label class="form-label">Nom de la recette :</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Image de la recette :</label>
            <input type="file" name="image" class="form-control" accept="image/*">
        </div>
        <div class="mb-3">
            <label class="form-label">Description :</label>
            <textarea name="description" class="form-control"></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Portions :</label>
            <input type="number" name="portions" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Temps de préparation (min) :</label>
            <input type="number" name="preparation_time" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Temps de cuisson (min) :</label>
            <input type="number" name="cooking_time" class="form-control" required>
        </div>

        <h4>🛒 Ingrédients</h4>
        <div id="ingredients-container"></div>
        <button type="button" id="ajouterIngredient" class="btn btn-success">+ Ajouter un ingrédient</button>

        <div class="mt-4">
            <button type="submit" name="add_recette" class="btn btn-primary">✅ Valider la recette</button>
            <a href="recettes.php" class="btn btn-secondary">❌ Annuler</a>
        </div>
    </form>
</div>

<script>
    $(document).ready(function () {
        // Autocomplétion pour les ingrédients
        $(document).on("keyup", ".ingredient-input", function () {
            let input = $(this);
            let search = input.val();
            if (search.length > 1) {
                $.ajax({
                    url: "autocomplete_ingredients.php",
                    method: "POST",
                    data: {query: search},
                    success: function (data) {
                        let resultBox = input.next(".suggestions");
                        resultBox.html(data);
                        resultBox.show();
                    }
                });
            }
        });

        // Sélection d'un ingrédient depuis la liste de suggestions
        $(document).on("click", ".suggestion-item", function () {
            let selected = $(this).text();
            $(this).closest(".ingredient-row").find(".ingredient-input").val(selected);
            $(".suggestions").hide();
        });

        // Ajout dynamique d’un ingrédient avec autocomplétion
        $("#ajouterIngredient").on("click", function () {
            let index = $(".ingredient-row").length;
            $("#ingredients-container").append(`
                <div class="ingredient-row position-relative mt-2">
                    <input type="text" name="ingredients[${index}][name]" class="form-control ingredient-input" placeholder="Nom de l'ingrédient" required>
                    <div class="suggestions position-absolute bg-white border" style="display:none;"></div>
                    <input type="number" name="ingredients[${index}][quantity]" class="form-control mt-1" placeholder="Quantité" required>
                    <input type="text" name="ingredients[${index}][unit]" class="form-control mt-1" placeholder="Unité (g, ml, pièces...)" required>
                    <button type="button" class="btn btn-danger remove-ingredient mt-1">❌</button>
                </div>
            `);
        });

        // Suppression d’un ingrédient ajouté
        $(document).on("click", ".remove-ingredient", function () {
            $(this).parent().remove();
        });
    });
</script>

</body>
</html>
