<?php
require_once '../backend/classes/Database.php';
require_once '../backend/classes/ListeCourses.php';
require_once '../backend/classes/Produit.php';

$pdo = Database::getInstance()->getConnection();
$listeCoursesManager = new ListeCourses($pdo);
$produitManager = new Produit($pdo);
$listeCourses = $listeCoursesManager->getListeCourses();

// 📌 Initialisation du prix total
$totalCourses = 0;

// 📌 Gestion de l'achat d'un produit et ajout au stock
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["mark_as_bought"])) {
    $id = $_POST["id"];
    $name = $_POST["name"];
    $quantity = (int) $_POST["quantity"];
    $unit = $_POST["unit"];

    // Ajouter le produit au stock
    $produitManager->ajouterProduit($name, $quantity, $unit, null); // Pas de date de péremption par défaut

    // Supprimer le produit de la liste de courses
    $listeCoursesManager->supprimerProduit($id);

    header("Location: liste_courses.php?success=Produit ajouté au stock !");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste de Courses - SmartPantry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container mt-5">
    <h2 class="page-title">🛒 Liste de Courses</h2>

    <?php if (!empty($listeCourses)): ?>
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Produit</th>
                    <th>Quantité</th>
                    <th>Prix estimé</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($listeCourses as $produit) : ?>
                    <?php
                    $nom = $produit['name'];
                    $quantite = $produit['quantity'];
                    $uniteRecette = $produit['unit'];

                    // 📌 Récupération des infos depuis la table ciqual_aliments
                    $stmt = $pdo->prepare("SELECT * FROM ciqual_aliments WHERE nom_aliment = ?");
                    $stmt->execute([$nom]);
                    $aliment = $stmt->fetch();

                    if ($aliment) {
                        $uniteAchat = $aliment['unite_achat']; // L'unité d'achat définie dans la table ciqual
                        $conversionUnite = (float) $aliment['conversion_unite'];
                        $prix100g = (float) $aliment['prix_100g'];

                        // ✅ Vérification des valeurs récupérées
                        if ($prix100g > 0) {
                            // ✅ Comparaison des unités entre la recette et la BDD
                            if ($uniteAchat === 'piece' && $uniteRecette === 'piece') {
                                // L'ingrédient est compté en pièces dans les deux cas → on utilise la conversion
                                $prixTotal = ($prix100g * ($conversionUnite / 100)) * $quantite;
                            } elseif ($uniteAchat === 'piece' && $uniteRecette === 'g') {
                                // L'ingrédient est vendu à la pièce mais utilisé en grammes → on prend juste le prix/100g
                                $prixTotal = ($prix100g / 100) * $quantite;
                            } elseif ($uniteAchat === 'L' && $uniteRecette === 'L') {
                                // L'ingrédient est vendu en litres et utilisé en litres → on utilise la conversion
                                $prixTotal = ($prix100g * ($conversionUnite / 100)) * $quantite;
                            } elseif ($uniteRecette === 'g') {
                                // L'ingrédient est vendu au poids et utilisé au poids → prix normal
                                $prixTotal = ($prix100g / 100) * $quantite;
                            }
                        }

                        // Ajout au total de la liste de courses
                        $totalCourses += $prixTotal;
                    } else {
                        $prixTotal = 0;
                    }
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($produit['name']); ?></td>
                        <td><?= htmlspecialchars($produit['quantity']) . ' ' . htmlspecialchars($produit['unit']); ?></td>
                        <td class="text-end"><strong><?= number_format($prixTotal, 2, ',', ''); ?>€</strong></td>
                        <td>
                            <form method="POST" action="" class="inline-form d-inline">
                                <input type="hidden" name="id" value="<?= $produit['id']; ?>">
                                <input type="hidden" name="name" value="<?= htmlspecialchars($produit['name']); ?>">
                                <input type="hidden" name="quantity" value="<?= $produit['quantity']; ?>">
                                <input type="hidden" name="unit" value="<?= htmlspecialchars($produit['unit']); ?>">
                                <button type="submit" name="mark_as_bought" class="btn btn-success btn-sm">✔️ Acheté</button>
                            </form>
                            <a href="liste_courses.php?delete=<?= $produit['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Voulez-vous vraiment supprimer cet article ?');">🗑️ Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr class="table-success">
                    <td colspan="2" class="text-end"><strong>Total estimé :</strong></td>
                    <td class="text-end"><strong><?= number_format($totalCourses, 2, ',', ''); ?>€</strong></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    <?php else: ?>
        <p class="no-items text-center">Aucun produit dans la liste de courses.</p>
    <?php endif; ?>
</div>

</body>
</html>
