<?php
require_once '../backend/classes/Database.php';
require_once '../backend/classes/Recette.php';

$pdo = Database::getInstance()->getConnection();
$recette = new Recette($pdo);
$recettes = $recette->getRecettes();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recettes - SmartPantry</title>
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container mt-5">
    <h2 class="page-title">📖 Liste des Recettes</h2>

    <a href="ajouter_recette.php" class="btn btn-primary add-recipe-btn">➕ Ajouter une recette</a>

    <div class="recipe-list">
        <?php if (!empty($recettes)): ?>
            <?php foreach ($recettes as $rec) : ?>
                <div class="recipe-card">
                    <img src="<?= $rec['image'] ?: 'default.jpg' ?>" alt="<?= htmlspecialchars($rec['name']); ?>" class="recipe-img">
                    <div class="recipe-info">
                        <h3><?= htmlspecialchars($rec['name']); ?></h3>
                        <p><strong>🍽️ Portions :</strong> <?= htmlspecialchars($rec['portions']); ?></p>
                        <p><strong>⏳ Temps :</strong> <?= htmlspecialchars($rec['preparation_time'] + $rec['cooking_time']); ?> min</p>
                        <a href="details_recette.php?id=<?= $rec['id']; ?>" class="btn btn-outline-primary">👀 Voir</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-recipes">Aucune recette trouvée.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
