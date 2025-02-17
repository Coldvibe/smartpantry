<?php
require_once 'Database.php';

class Produit {
    private $pdo; // Déclaration de la variable de connexion

    public function __construct() {
        // Récupération de l'instance de connexion
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function getProduits() {
        $stmt = $this->pdo->query("SELECT * FROM produits");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // ✅ Ajouter un produit
    public function ajouterProduit(string $name, int $quantity, string $unit, string $expiration_date) {
        $stmt = $this->pdo->prepare("INSERT INTO produits (name, quantity, unit, expiration_date) VALUES (:name, :quantity, :unit, :expiration_date)");
        $stmt->execute([
            'name' => $name,
            'quantity' => $quantity,
            'unit' => $unit,
            'expiration_date' => $expiration_date
        ]);
    }

    // ✅ Supprimer un produit
    public function supprimerProduit(int $id) {
        $stmt = $this->pdo->prepare("DELETE FROM produits WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }

    // ✅ Modifier un produit
    public function modifierProduit(int $id, string $name, int $quantity, string $unit, string $expiration_date) {
        $stmt = $this->pdo->prepare("UPDATE produits SET name = :name, quantity = :quantity, unit = :unit, expiration_date = :expiration_date WHERE id = :id");
        $stmt->execute([
            'id' => $id,
            'name' => $name,
            'quantity' => $quantity,
            'unit' => $unit,
            'expiration_date' => $expiration_date
        ]);
    }
    public function getProduit($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM produits WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    

    // ✅ Getters pour récupérer les propriétés
    public function getId(): int {
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getQuantity(): int {
        return $this->quantity;
    }

    public function getUnit(): string {
        return $this->unit;
    }

    public function getExpirationDate(): string {
        return $this->expiration_date;
    }
}
