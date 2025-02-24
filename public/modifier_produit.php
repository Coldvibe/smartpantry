<?php
require_once '../backend/classes/Database.php';
require_once '../backend/classes/Produit.php';

$pdo = Database::getInstance()->getConnection();
$produitManager = new Produit($pdo);

// Vérifier si un ID de produit est passé en paramètre
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("⚠️ ID de produit manquant.");
}

$produit_id = $_GET['id'];
$produitData = $produitManager->getProduit($produit_id);

// Vérifier si le produit existe
if (!$produitData) {
    die("⚠️ Produit introuvable.");
}

// 📌 Traitement du formulaire de modification
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = trim($_POST['nom']);
    $quantite = (int) $_POST['quantite'];
    $unite = trim($_POST['unite']);
    $date_peremption = $_POST['date_peremption'];

    // Mise à jour du produit
    $produitManager->modifierProduit($produit_id, $nom, $quantite, $unite, $date_peremption);

    header("Location: stock.php?success=Produit mis à jour !");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Produit - SmartPantry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container mt-5">
    <h2 class="page-title">✏️ Modifier Produit</h2>
    <form method="POST" action="" class="form-container">
        <div class="mb-3 position-relative">
            <label class="form-label">Nom du produit :</label>
            <input type="text" name="nom" id="produit-input" class="form-control" value="<?= htmlspecialchars($produitData['name']); ?>" required>
            <div id="suggestions" class="suggestions position-absolute bg-white border" style="display:none;"></div>
        </div>
        <div class="mb-3">
            <label class="form-label">Quantité :</label>
            <input type="number" name="quantite" class="form-control" value="<?= $produitData['quantity']; ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Unité :</label>
            <input type="text" name="unite" class="form-control" value="<?= htmlspecialchars($produitData['unit']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Date de péremption :</label>
            <input type="date" name="date_peremption" class="form-control" value="<?= $produitData['expiration_date']; ?>">
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">✅ Mettre à jour</button>
            <a href="stock.php" class="btn btn-secondary">❌ Annuler</a>
        </div>
    </form>
</div>

<script>
    $(document).ready(function () {
        // Autocomplétion pour le champ produit
        $("#produit-input").on("keyup", function () {
            let input = $(this);
            let search = input.val();
            if (search.length > 1) {
                $.ajax({
                    url: "autocomplete_produits.php",
                    method: "POST",
                    data: {query: search},
                    success: function (data) {
                        $("#suggestions").html(data).show();
                    }
                });
            }
        });

        // Sélection d'un produit depuis la liste de suggestions
        $(document).on("click", ".suggestion-item", function () {
            $("#produit-input").val($(this).text());
            $("#suggestions").hide();
        });

        // Cacher les suggestions si on clique en dehors
        $(document).on("click", function (event) {
            if (!$(event.target).closest("#produit-input, #suggestions").length) {
                $("#suggestions").hide();
            }
        });
    });
</script>

</body>
</html>
