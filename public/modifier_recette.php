<?php
require_once '../backend/classes/Database.php';
require_once '../backend/classes/Recette.php';

// Vérifie si un ID de recette est passé en paramètre
if (!isset($_GET['id'])) {
    die("ID de recette manquant.");
}

$pdo = Database::getInstance()->getConnection();
$recette = new Recette($pdo);
$recetteData = $recette->getRecette($_GET['id']);

// Vérifie si la recette existe
if (!$recetteData) {
    die("Recette introuvable.");
}

// Traitement du formulaire de modification
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $portions = $_POST['portions'];
    $preparation_time = $_POST['preparation_time'];
    $cooking_time = $_POST['cooking_time'];

    // Mise à jour de la recette
    $recette->modifierRecette($_GET['id'], $nom, $description, $portions, $preparation_time, $cooking_time);
    header("Location: recettes.php"); // Redirection après modification
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Recette</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Modifier Recette</h2>
    <form action="" method="post">
        <label for="nom">Nom :</label>
        <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($recetteData['name']) ?>" required>

        <label for="description">Description :</label>
        <textarea id="description" name="description"><?= htmlspecialchars($recetteData['description']) ?></textarea>

        <label for="portions">Portions :</label>
        <input type="number" id="portions" name="portions" value="<?= htmlspecialchars($recetteData['portions']) ?>" required>

        <label for="preparation_time">Temps de préparation (min) :</label>
        <input type="number" id="preparation_time" name="preparation_time" value="<?= htmlspecialchars($recetteData['preparation_time']) ?>" required>

        <label for="cooking_time">Temps de cuisson (min) :</label>
        <input type="number" id="cooking_time" name="cooking_time" value="<?= htmlspecialchars($recetteData['cooking_time']) ?>" required>

        <button type="submit">Modifier</button>
    </form>
</body>
</html>
