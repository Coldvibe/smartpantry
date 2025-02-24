<?php
require_once "Database.php";

class ListeCourses {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // 📌 Récupérer la liste complète des produits dans la liste de courses
    public function getListeCourses() {
        $stmt = $this->pdo->query("SELECT * FROM liste_courses ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 📌 Ajouter un produit à la liste de courses
    public function ajouterProduit($name, $quantity, $unit) {
        // Vérifier si le produit existe déjà dans la liste
        $stmt = $this->pdo->prepare("SELECT * FROM liste_courses WHERE name = :name");
        $stmt->execute(['name' => $name]);
        $produit_existant = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($produit_existant) {
            // Produit déjà présent -> Incrémenter la quantité
            $nouvelle_quantite = $produit_existant['quantity'] + $quantity;
            $stmt = $this->pdo->prepare("UPDATE liste_courses SET quantity = :quantity WHERE id = :id");
            return $stmt->execute([
                'quantity' => $nouvelle_quantite,
                'id' => $produit_existant['id']
            ]);
        } else {
            // Nouveau produit -> L'ajouter à la liste
            $stmt = $this->pdo->prepare("INSERT INTO liste_courses (name, quantity, unit) VALUES (:name, :quantity, :unit)");
            return $stmt->execute([
                'name' => $name,
                'quantity' => $quantity,
                'unit' => $unit
            ]);
        }
    }

    // 📌 Supprimer un produit de la liste de courses
    public function supprimerProduit($id) {
        $stmt = $this->pdo->prepare("DELETE FROM liste_courses WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
?>
