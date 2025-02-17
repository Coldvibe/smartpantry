<?php
require_once '../backend/classes/Database.php';
require_once '../backend/classes/Recette.php';

$pdo = Database::getInstance()->getConnection();

$recette = new Recette($pdo);

// Vérifie si un ID de recette est passé en paramètre
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<p>Erreur : ID de recette non spécifié.</p>";
    echo "<a href='recettes.php'>Retour</a>";
    exit;
}

$recette_id = $_GET['id'];
$recetteDetails = $recette->getRecette($recette_id);

// Vérifie si la recette existe
if (!$recetteDetails) {
    echo "<p>Erreur : Recette introuvable.</p>";
    echo "<a href='recettes.php'>Retour</a>";
    exit;
}

// Récupération des ingrédients de la recette
$ingredients = $recette->getIngredientsByRecette($recette_id);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de la Recette</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center"><?php echo htmlspecialchars($recetteDetails['name']); ?></h2>
        <p><strong>Description :</strong> <?php echo htmlspecialchars($recetteDetails['description']); ?></p>
        <p><strong>Portions :</strong> <?php echo $recetteDetails['portions']; ?></p>
        <p><strong>Temps de préparation :</strong> <?php echo $recetteDetails['preparation_time']; ?> min</p>
        <p><strong>Temps de cuisson :</strong> <?php echo $recetteDetails['cooking_time']; ?> min</p>

        <h4 class="mt-4">Ingrédients</h4>
        <?php if (!empty($ingredients)): ?>
            <ul class="list-group">
                <?php foreach ($ingredients as $ingredient): ?>
                    <li class="list-group-item">
                        <?php echo htmlspecialchars($ingredient['name']) . " - " . htmlspecialchars($ingredient['quantity']) . " " . htmlspecialchars($ingredient['unit']); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Aucun ingrédient trouvé pour cette recette.</p>
        <?php endif; ?>
        <form action="valider_recette.php" method="POST">
    <input type="hidden" name="recette_id" value="<?php echo htmlspecialchars($recetteDetails['id']); ?>">
    <button type="submit" class="btn btn-success">✅ Valider la recette</button>
</form>

        <div class="mt-4">
            <a href="recettes.php" class="btn btn-secondary">Retour</a>
        </div>
    </div>
</body>
</html>
