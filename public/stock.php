<?php
require_once '../backend/classes/Database.php';
require_once '../backend/classes/Produit.php';

$db = Database::getInstance();
$produitManager = new Produit($db);
$produits = $produitManager->getProduits();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartPantry - Stock</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <h2>Stock des Produits</h2>
        <a href="ajouter_produit.php" class="btn btn-success mb-3">Ajouter un produit</a>

        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Nom</th>
                    <th>Quantité</th>
                    <th>Unité</th>
                    <th>Date de péremption</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produits as $produit) : ?>
                    <tr>
                        <td><?= htmlspecialchars($produit['name']); ?></td> <!-- Utilisation de la clé du tableau ✅ -->
                        

                        <td><?= htmlspecialchars($produit['quantity']); ?></td> <!-- Utilisation de la clé du tableau ✅ -->
                        <td><?= htmlspecialchars($produit['unit']); ?></td> <!-- Utilisation de la clé du tableau ✅ -->
                        <td><?= htmlspecialchars($produit['expiration_date']); ?></td> <!-- Utilisation de la clé du tableau ✅ -->
                        <td>
                            <a href="modifier_produit.php?id=<?= htmlspecialchars($produit['id']); ?>" class="btn btn-warning btn-sm">Modifier</a>
                            <a href="supprimer_produit.php?id=<?= htmlspecialchars($produit['id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Voulez-vous vraiment supprimer ce produit ?');">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
