<?php
require_once '../backend/classes/Database.php';
require_once '../backend/classes/Recette.php';
require_once '../backend/classes/Ingredient.php';

$pdo = Database::getInstance()->getConnection();
$recetteObj = new Recette($pdo);
$ingredientObj = new Ingredient();

// Vérifier si un ID de recette est passé en paramètre
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("⚠️ ID de recette manquant.");
}

$recette_id = $_GET['id'];
$recetteData = $recetteObj->getRecette($recette_id);
$ingredients = $ingredientObj->getIngredientsByRecette($recette_id);

// Vérifier si la recette existe
if (!$recetteData) {
    die("⚠️ Recette introuvable.");
}

// 📌 Traitement du formulaire de modification
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST["name"];
    $description = $_POST["description"];
    $portions = $_POST["portions"];
    $preparation_time = $_POST["preparation_time"];
    $cooking_time = $_POST["cooking_time"];
    $updatedIngredients = $_POST["ingredients"];

    // 📷 Gestion de l'upload de l'image
    $image = $recetteData["image"];
    if (!empty($_FILES["image"]["name"])) {
        $image = basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], "assets/images/recettes/" . $image);
    }

    // 📌 Mise à jour de la recette
    $recetteObj->modifierRecette($recette_id, $nom, $description, $portions, $preparation_time, $cooking_time, $image);

    // Suppression des anciens ingrédients pour éviter les doublons
    $pdo->prepare("DELETE FROM ingredients_recettes WHERE recette_id = ?")->execute([$recette_id]);

    // Ajout des nouveaux ingrédients
    foreach ($updatedIngredients as $ing) {
        $ingredientObj->ajouterIngredient($recette_id, $ing["name"], $ing["quantity"], $ing["unit"]);
    }

    header("Location: recettes.php?success=Recette mise à jour avec succès !");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Recette</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container mt-5">
    <h2 class="page-title">✏️ Modifier Recette</h2>
    <form method="POST" action="" enctype="multipart/form-data" class="form-container">
        <div class="mb-3">
            <label class="form-label">Nom de la recette :</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($recetteData['name']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Image de la recette :</label>
            <input type="file" name="image" class="form-control" accept="image/*">
            <p>Image actuelle :</p>
            <img src="assets/images/recettes/<?= $recetteData['image']; ?>" alt="Recette" height="80" class="recipe-preview">
        </div>
        <div class="mb-3">
            <label class="form-label">Description :</label>
            <textarea name="description" class="form-control"><?= htmlspecialchars($recetteData['description']); ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Portions :</label>
            <input type="number" name="portions" class="form-control" value="<?= $recetteData['portions']; ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Temps de préparation (min) :</label>
            <input type="number" name="preparation_time" class="form-control" value="<?= $recetteData['preparation_time']; ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Temps de cuisson (min) :</label>
            <input type="number" name="cooking_time" class="form-control" value="<?= $recetteData['cooking_time']; ?>" required>
        </div>

        <h4>🛒 Ingrédients</h4>
        <div id="ingredients-container">
            <?php foreach ($ingredients as $index => $ingredient) : ?>
                <div class="ingredient-row position-relative">
                    <input type="text" name="ingredients[<?= $index; ?>][name]" class="form-control ingredient-input"
                           value="<?= htmlspecialchars($ingredient['name']); ?>" required>
                    <div class="suggestions position-absolute bg-white border" style="display:none;"></div>
                    <input type="number" name="ingredients[<?= $index; ?>][quantity]" class="form-control"
                           value="<?= $ingredient['quantity']; ?>" required>
                    <input type="text" name="ingredients[<?= $index; ?>][unit]" class="form-control"
                           value="<?= htmlspecialchars($ingredient['unit']); ?>" required>
                    <button type="button" class="btn btn-danger remove-ingredient">❌</button>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" id="ajouterIngredient" class="btn btn-success">+ Ajouter un ingrédient</button>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">✅ Mettre à jour</button>
            <a href="recettes.php" class="btn btn-secondary">❌ Annuler</a>
        </div>
    </form>
</div>

<script>
    $(document).ready(function () {
        // Autocomplétion des ingrédients
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

        // Ajout d’un ingrédient
        $("#ajouterIngredient").on("click", function () {
            let index = $(".ingredient-row").length;
            $("#ingredients-container").append(`
                <div class="ingredient-row position-relative">
                    <input type="text" name="ingredients[${index}][name]" class="form-control ingredient-input" placeholder="Nom de l'ingrédient" required>
                    <div class="suggestions position-absolute bg-white border" style="display:none;"></div>
                    <input type="number" name="ingredients[${index}][quantity]" class="form-control" placeholder="Quantité" required>
                    <input type="text" name="ingredients[${index}][unit]" class="form-control" placeholder="Unité (g, ml, pièces...)" required>
                    <button type="button" class="btn btn-danger remove-ingredient">❌</button>
                </div>
            `);
        });

        // Suppression d'un ingrédient
        $(document).on("click", ".remove-ingredient", function () {
            $(this).parent().remove();
        });
    });
</script>

</body>
</html>
