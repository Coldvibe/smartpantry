<?php
require_once '../backend/classes/Database.php';
require_once '../backend/classes/Recette.php';
require_once '../backend/classes/ListeCourses.php';

$pdo = Database::getInstance()->getConnection();
$recetteObj = new Recette($pdo);
$listeCoursesManager = new ListeCourses($pdo);

// Vérifier si un ID de recette est passé en paramètre
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("⚠️ ID de recette manquant.");
}

$recette_id = $_GET['id'];
$recetteDetails = $recetteObj->getRecette($recette_id);
$ingredients = $recetteObj->getIngredientsByRecette($recette_id);

// Vérifier si la recette existe
if (!$recetteDetails) {
    die("⚠️ Recette introuvable.");
}

// Récupérer la liste des produits déjà ajoutés à la liste de courses
$listeCourses = $listeCoursesManager->getListeCourses();
$produitsListeCourses = array_column($listeCourses, 'name'); // Tableau des noms des produits déjà dans la liste

// 📌 Initialisation du prix total de la recette
$prixTotalRecette = 0.0;

// 📌 Traitement de l'ajout automatique à la liste de courses
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_to_list"])) {
    $name = $_POST["name"];
    $quantity = $_POST["quantity"];
    $unit = $_POST["unit"];

    $listeCoursesManager->ajouterProduit($name, $quantity, $unit);
    header("Location: details_recette.php?id=$recette_id&success=Ingrédient ajouté à la liste !");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détails Recette - SmartPantry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container mt-5">
    <h2 class="page-title"><?= htmlspecialchars($recetteDetails['name']); ?></h2>
    <p><strong>📝 Description :</strong> <?= htmlspecialchars($recetteDetails['description']); ?></p>
    <p><strong>🍽️ Portions :</strong> <?= $recetteDetails['portions']; ?></p>
    <p><strong>⏳ Préparation :</strong> <?= $recetteDetails['preparation_time']; ?> min</p>
    <p><strong>🔥 Cuisson :</strong> <?= $recetteDetails['cooking_time']; ?> min</p>

    <h4>🛒 Ingrédients</h4>

    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Ingrédient</th>
                <th>Quantité</th>
                <th>Prix estimé</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($ingredients as $ingredient) :
                $nom = htmlspecialchars($ingredient['name']);
                $quantite = (float) $ingredient['quantity'];
                $uniteRecette = htmlspecialchars($ingredient['unit']); // L'unité spécifiée dans la recette

                // Récupération des infos de conversion et prix depuis la table ciqual_aliments
                $stmt = $pdo->prepare("SELECT unite_achat, conversion_unite, prix_100g FROM ciqual_aliments WHERE nom_aliment = ?");
                $stmt->execute([$nom]);
                $aliment = $stmt->fetch(PDO::FETCH_ASSOC);

                $prixTotal = 0.0;

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

                    $prixTotalRecette += $prixTotal;
                }

                $classeCSS = ($ingredient['stock_quantity'] >= $ingredient['quantity']) ? 'table-success' : 'table-danger';
            ?>
                <tr class="<?= $classeCSS; ?>">
                    <td><?= $nom; ?></td>
                    <td><?= $quantite . " " . $uniteRecette; ?></td>
                    <td class="text-end"><strong><?= number_format($prixTotal, 2); ?>€</strong></td>
                    <td>
                        <?php if ($ingredient['stock_quantity'] < $ingredient['quantity']) : ?>
                            <?php if (in_array($nom, $produitsListeCourses)) : ?>
                                <span class="text-muted">🛒 Déjà dans la liste</span>
                            <?php else : ?>
                                <form method="POST" action="" class="d-inline">
                                    <input type="hidden" name="name" value="<?= $nom; ?>">
                                    <input type="hidden" name="quantity" value="<?= $quantite; ?>">
                                    <input type="hidden" name="unit" value="<?= $uniteRecette; ?>">
                                    <button type="submit" name="add_to_list" class="btn btn-warning btn-sm">➕ Ajouter</button>
                                </form>
                            <?php endif; ?>
                        <?php else : ?>
                            <span class="text-success">✔️ En stock</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr class="table-success">
                <td colspan="2" class="text-end"><strong>Prix total de la recette :</strong></td>
                <td class="text-end"><strong><?= number_format($prixTotalRecette, 2); ?>€</strong></td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div class="mt-4">
        <a href="recettes.php" class="btn btn-secondary">⬅️ Retour aux Recettes</a>
        <a href="modifier_recette.php?id=<?= $recette_id; ?>" class="btn btn-primary">✏️ Modifier la Recette</a>
    </div>
</div>

</body>
</html>
