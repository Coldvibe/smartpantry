<?php
require_once "Database.php";

class Ingredient {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    // Ajouter un ingrédient à une recette
    public function ajouterIngredient($recette_id, $name, $quantity, $unit) {
        $stmt = $this->pdo->prepare("
            INSERT INTO ingredients_recettes (recette_id, name, quantity, unit)
            VALUES (:recette_id, :name, :quantity, :unit)
        ");
        return $stmt->execute([
            ':recette_id' => $recette_id,
            ':name' => $name,
            ':quantity' => $quantity,
            ':unit' => $unit
        ]);
    }

    // Récupérer les ingrédients d'une recette spécifique
    public function getIngredientsByRecette($recette_id) {
        $stmt = $this->pdo->prepare("
            SELECT ir.name, ir.quantity, ir.unit, p.quantity AS stock_quantity
            FROM ingredients_recettes ir
            LEFT JOIN produits p ON ir.name = p.name
            WHERE ir.recette_id = :recette_id
        ");
        $stmt->execute([':recette_id' => $recette_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Vérifier si une recette est réalisable en fonction du stock disponible
    public function recetteRealisable($recette_id) {
        $ingredients = $this->getIngredientsByRecette($recette_id);
        
        foreach ($ingredients as $ing) {
            if ($ing['stock_quantity'] === null || $ing['stock_quantity'] < $ing['quantity']) {
                return false; // Pas assez d'ingrédients en stock
            }
        }
        return true; // Tous les ingrédients sont disponibles
    }
}
?>
