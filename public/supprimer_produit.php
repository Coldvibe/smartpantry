<?php
require_once '../backend/classes/Database.php';
require_once '../backend/classes/Produit.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $produit = new Produit();

    if ($produit->supprimerProduit($id)) {
        header("Location: stock.php?success=Produit supprimé !");
        exit();
    } else {
        header("Location: stock.php?error=Erreur lors de la suppression.");
        exit();
    }
}
?>
