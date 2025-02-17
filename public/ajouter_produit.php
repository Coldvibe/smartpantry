<?php
require_once '../backend/classes/Database.php';
require_once '../backend/classes/Produit.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'];
    $quantite = $_POST['quantite'];
    $unite = $_POST['unite'];
    $date_peremption = $_POST['date_peremption'];

    $produit = new Produit();
    if ($produit->ajouterProduit($nom, $quantite, $unite, $date_peremption)) {
        header("Location: stock.php?success=Produit ajouté !");
        exit();
    } else {
        header("Location: stock.php?error=Erreur lors de l'ajout.");
        exit();
    }
}
?>

<!-- Formulaire HTML -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Produit</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Ajouter un Produit</h2>
    <form method="POST" action="ajouter_produit.php">
        <label>Nom :</label>
        <input type="text" name="nom" required><br>

        <label>Quantité :</label>
        <input type="number" name="quantite" required><br>

        <label>Unité :</label>
        <input type="text" name="unite" required><br>

        <label>Date de péremption :</label>
        <input type="date" name="date_peremption"><br>

        <button type="submit">Ajouter</button>
    </form>
</body>
</html>
