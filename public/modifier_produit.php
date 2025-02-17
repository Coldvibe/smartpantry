<?php
require_once '../backend/classes/Database.php';
require_once '../backend/classes/Produit.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];
    $nom = $_POST['nom'];
    $quantite = $_POST['quantite'];
    $unite = $_POST['unite'];
    $date_peremption = $_POST['date_peremption'];

    $produit = new Produit();
    if ($produit->modifierProduit($id, $nom, $quantite, $unite, $date_peremption)) {
        header("Location: stock.php?success=Produit modifié !");
        exit();
    } else {
        header("Location: stock.php?error=Erreur lors de la modification.");
        exit();
    }
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $produit = new Produit();
    $data = $produit->getProduit($id);
} else {
    header("Location: stock.php");
    exit();
}
?>

<!-- Formulaire HTML -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Produit</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Modifier Produit</h2>
    <form method="POST" action="modifier_produit.php">
        <input type="hidden" name="id" value="<?= $data['id']; ?>">
        
        <label>Nom :</label>
        <input type="text" name="nom" value="<?= $data['name']; ?>" required><br>

        <label>Quantité :</label>
        <input type="number" name="quantite" value="<?= $data['quantity']; ?>" required><br>

        <label>Unité :</label>
        <input type="text" name="unite" value="<?= $data['unit']; ?>" required><br>

        <label>Date de péremption :</label>
        <input type="date" name="date_peremption" value="<?= $data['expiration_date']; ?>"><br>

        <button type="submit">Modifier</button>
    </form>
</body>
</html>
