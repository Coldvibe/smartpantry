<?php
require_once '../backend/classes/Database.php';
require_once '../backend/classes/Produit.php';

$pdo = Database::getInstance()->getConnection();
$produitObj = new Produit($pdo);

// Vérification de l'ID et suppression du produit
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $produit_id = intval($_GET['id']);

    // Supprimer le produit
    if ($produitObj->supprimerProduit($produit_id)) {
        header("Location: stock.php?success=Produit supprimé !");
        exit();
    } else {
        header("Location: stock.php?error=Erreur lors de la suppression !");
        exit();
    }
} else {
    // Redirection en cas d'ID invalide
    header("Location: stock.php?error=ID de produit invalide !");
    exit();
}
?>
