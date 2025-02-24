<?php
require_once 'Database.php';

class Produit {
    private $pdo; 

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    // ✅ Récupérer tous les produits
    public function getProduits() {
        $stmt = $this->pdo->query("SELECT * FROM produits ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // ✅ Récupérer un produit par son nom normalisé
    public function getProduitByName($name) {
        $stmt = $this->pdo->prepare("SELECT * FROM produits WHERE LOWER(REPLACE(name, ' ', '')) LIKE LOWER(REPLACE(:name, ' ', '')) LIMIT 1");
        $stmt->execute(['name' => $name]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }



    public function ajouterProduit($name, $quantity, $unit, $expiration_date = null) {
        // Vérifier si le produit existe déjà dans le stock
        $stmt = $this->pdo->prepare("SELECT * FROM produits WHERE name = :name");
        $stmt->execute(['name' => $name]);
        $produit_existant = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($produit_existant) {
            // Produit déjà en stock -> Incrémenter la quantité
            $nouvelle_quantite = $produit_existant['quantity'] + $quantity;
            $stmt = $this->pdo->prepare("UPDATE produits SET quantity = :quantity WHERE id = :id");
            return $stmt->execute([
                'quantity' => $nouvelle_quantite,
                'id' => $produit_existant['id']
            ]);
        } else {
            // Nouveau produit -> L'ajouter au stock
            $stmt = $this->pdo->prepare("INSERT INTO produits (name, quantity, unit, expiration_date) VALUES (:name, :quantity, :unit, :expiration_date)");
            return $stmt->execute([
                'name' => $name,
                'quantity' => $quantity,
                'unit' => $unit,
                'expiration_date' => $expiration_date
            ]);
        }
    }
    

    

    // ✅ Supprimer un produit
    public function supprimerProduit(int $id) {
        $stmt = $this->pdo->prepare("DELETE FROM produits WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    // ✅ Modifier un produit
    public function modifierProduit(int $id, string $name, int $quantity, string $unit, ?string $expiration_date) {
        $stmt = $this->pdo->prepare("
            UPDATE produits 
            SET name = :name, quantity = :quantity, unit = :unit, expiration_date = :expiration_date 
            WHERE id = :id
        ");
        return $stmt->execute([
            ':id' => $id,
            ':name' => htmlspecialchars(trim($name)),
            ':quantity' => max(0, intval($quantity)), 
            ':unit' => htmlspecialchars(trim($unit)),
            ':expiration_date' => $expiration_date ? htmlspecialchars(trim($expiration_date)) : null
        ]);
    }

    // ✅ Récupérer un produit par son ID
    public function getProduit(int $id) {
        $stmt = $this->pdo->prepare("SELECT * FROM produits WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ✅ Déduire la quantité d’un produit (lorsqu’une recette est validée)
    public function deduireStock(string $name, int $quantity) {
        $stmt = $this->pdo->prepare("
            UPDATE produits 
            SET quantity = quantity - :quantity 
            WHERE name = :name AND quantity >= :quantity
        ");
        $stmt->execute([
            ':quantity' => max(0, intval($quantity)),
            ':name' => htmlspecialchars(trim($name))
        ]);

        if ($stmt->rowCount() == 0) {
            logError("Stock insuffisant pour " . $name);
            return false;
        }
        return true;
    }
}
?>
