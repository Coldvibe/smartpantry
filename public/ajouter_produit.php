<?php
require_once '../backend/classes/Database.php';
require_once '../backend/classes/Produit.php';

$pdo = Database::getInstance()->getConnection();
$produitManager = new Produit($pdo);

// 📌 Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = trim($_POST['nom']);
    $quantite = (int) $_POST['quantite'];
    $unite = trim($_POST['unite']);
    $date_peremption = $_POST['date_peremption'];

    // Vérifier si le produit existe déjà
    $produit_existant = $produitManager->getProduitByName($nom);

    if ($produit_existant) {
        // Produit existant -> Incrémenter la quantité
        $nouvelle_quantite = $produit_existant['quantity'] + $quantite;
        $produitManager->modifierProduit(
            $produit_existant['id'],
            $produit_existant['name'],
            $nouvelle_quantite,
            $produit_existant['unit'],
            $date_peremption
        );

        header("Location: stock.php?success=Quantité mise à jour !");
        exit();
    } else {
        // Nouveau produit -> Ajouter dans la base
        $produitManager->ajouterProduit($nom, $quantite, $unite, $date_peremption);
        header("Location: stock.php?success=Produit ajouté !");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Produit - SmartPantry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container mt-5">
    <h2 class="page-title">➕ Ajouter un Produit</h2>
    <form method="POST" action="" class="form-container">
        <div class="mb-3 position-relative">
            <label class="form-label">Nom du produit :</label>
            <input type="text" name="nom" id="produit-input" class="form-control" required>
            <div id="suggestions" class="suggestions position-absolute bg-white border" style="display:none;"></div>
        </div>
        <div class="mb-3">
            <label class="form-label">Quantité :</label>
            <input type="number" name="quantite" class="form-control" required>
        </div>
        <div class="mb-3">
        <label>Unité de mesure :</label>
<select name="unite" id="uniteSelect" class="form-control">
    <option value="g">Gramme (g)</option>
    <option value="L">Litre (L)</option>
    <option value="unit">Pièce (Unité)</option>
</select>

<div id="conversionDiv" style="display: none;">
    <label>Poids moyen en grammes :</label>
    <input type="number" name="conversion_unite" id="conversionInput" class="form-control" placeholder="Ex: 200g par avocat">
</div>
        </div>
        <div class="mb-3">
            <label class="form-label">Date de péremption :</label>
            <input type="date" name="date_peremption" class="form-control">
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">✅ Ajouter</button>
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
    document.getElementById('uniteSelect').addEventListener('change', function() {
    let conversionDiv = document.getElementById('conversionDiv');
    let conversionInput = document.getElementById('conversionInput');
    if (this.value !== 'g') {
        conversionDiv.style.display = 'block';
        conversionInput.required = true;
    } else {
        conversionDiv.style.display = 'none';
        conversionInput.required = false;
    }
});
</script>

</body>
</html>
