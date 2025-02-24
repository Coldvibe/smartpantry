<?php
require_once '../backend/classes/Database.php';
require_once '../backend/classes/Produit.php';

$pdo = Database::getInstance()->getConnection();
$produitManager = new Produit($pdo);
$produits = $produitManager->getProduits();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Stock - SmartPantry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container mt-5">
    <h2 class="page-title">📦 Gestion du Stock</h2>
    
    <a href="ajouter_produit.php" class="btn btn-primary mb-3">➕ Ajouter un produit</a>

    <?php if (!empty($produits)): ?>
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Produit</th>
                    <th>Quantité</th>
                    <th>Unité</th>
                    <th>Prix estimé</th>
                    <th>Date de péremption</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $prixTotalStock = 0;

                foreach ($produits as $produit) :
                    $nom = htmlspecialchars($produit['name']);
                    $quantite = (float) $produit['quantity'];
                    $uniteStock = htmlspecialchars($produit['unit']);
                    $datePeremption = $produit['expiration_date'] ? htmlspecialchars($produit['expiration_date']) : 'N/A';

                    // Récupération des infos de prix depuis la table ciqual_aliments
                    $stmt = $pdo->prepare("SELECT unite_achat, conversion_unite, prix_100g FROM ciqual_aliments WHERE nom_aliment = ?");
                    $stmt->execute([$nom]);
                    $aliment = $stmt->fetch(PDO::FETCH_ASSOC);

                    $prixTotal = 0.0;

                    if ($aliment) {
                        $uniteAchat = $aliment['unite_achat']; // L'unité d'achat définie dans la table ciqual
                        $conversionUnite = (float) $aliment['conversion_unite'];
                        $prix100g = (float) $aliment['prix_100g'];

                        // ✅ Comparaison des unités entre le stock et la BDD
                        if ($uniteAchat === 'piece' && $uniteStock === 'piece') {
                            // L'ingrédient est compté en pièces dans les deux cas → on utilise la conversion
                            $prixTotal = ($prix100g * ($conversionUnite / 100)) * $quantite;
                        } elseif ($uniteAchat === 'piece' && $uniteStock === 'g') {
                            // L'ingrédient est vendu à la pièce mais utilisé en grammes → on prend juste le prix/100g
                            $prixTotal = ($prix100g / 100) * $quantite;
                        } elseif ($uniteAchat === 'L' && $uniteStock === 'L') {
                            // L'ingrédient est vendu en litres et utilisé en litres → on utilise la conversion
                            $prixTotal = ($prix100g * ($conversionUnite / 100)) * $quantite;
                        } elseif ($uniteStock === 'g') {
                            // L'ingrédient est vendu au poids et utilisé au poids → prix normal
                            $prixTotal = ($prix100g / 100) * $quantite;
                        }
                    }

                    // Ajout au total du stock
                    $prixTotalStock += $prixTotal;
                ?>
                    <tr>
                        <td><?= $nom; ?></td>
                        <td><?= $quantite; ?></td>
                        <td><?= $uniteStock; ?></td>
                        <td class="text-end"><strong><?= number_format($prixTotal, 2); ?>€</strong></td>
                        <td><?= $datePeremption; ?></td>
                        <td>
                            <a href="modifier_produit.php?id=<?= $produit['id']; ?>" class="btn btn-warning btn-sm">✏️ Modifier</a>
                            <a href="supprimer_produit.php?id=<?= $produit['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Voulez-vous vraiment supprimer ce produit ?');">🗑️ Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr class="table-success">
                    <td colspan="3" class="text-end"><strong>Valeur totale du stock :</strong></td>
                    <td class="text-end"><strong><?= number_format($prixTotalStock, 2); ?>€</strong></td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>
    <?php else: ?>
        <p class="no-stock alert alert-warning">Aucun produit en stock.</p>
    <?php endif; ?>
</div>

</body>
</html>
