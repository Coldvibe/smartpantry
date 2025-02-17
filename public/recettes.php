<?php
require_once '../backend/classes/Recette.php';

$recette = new Recette();
$recettes = $recette->getRecettes();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartPantry - Recettes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container mt-4">
    <h2 class="text-center">Liste des Recettes</h2>
    <a href="ajouter_recette.php" class="btn btn-success mb-3">
        <i class="bi bi-plus-circle"></i> Ajouter une recette
    </a>
    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>Nom</th>
                <th>Portions</th>
                <th>Temps (Prépa + Cuisson)</th>
                <th>Ingrédients</th>
                <th>Disponibilité</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recettes as $rec) : ?>
                <tr>
                    <td><?= htmlspecialchars($rec['name']); ?></td>
                    <td><?= htmlspecialchars($rec['portions']); ?></td>
                    <td><?= htmlspecialchars($rec['preparation_time'] + $rec['cooking_time']) . " min"; ?></td>
                    <td>
                        <a href="details_recette.php?id=<?= $rec['id']; ?>" class="btn btn-info btn-sm">
                            <i class="bi bi-eye"></i> Voir
                        </a>
                    </td>
                    <td>
                        <?php if ($recette->recetteRealisable($rec['id'])): ?>
                            <span class="badge bg-success">Disponible</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Ingrédients manquants</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="modifier_recette.php?id=<?= $rec['id']; ?>" class="btn btn-warning btn-sm">
                            <i class="bi bi-pencil"></i> Modifier
                        </a>
                        <a href="supprimer_recette.php?id=<?= $rec['id']; ?>" class="btn btn-danger btn-sm">
                            <i class="bi bi-trash"></i> Supprimer
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
