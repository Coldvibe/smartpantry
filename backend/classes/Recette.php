<?php
require_once "Database.php"; // Assurez-vous que la classe Database est bien incluse

class Recette {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    public function supprimerRecette($recette_id) {
        // Supprimer les ingrédients liés à la recette
        $stmt = $this->pdo->prepare("DELETE FROM ingredients_recettes WHERE recette_id = ?");
        $stmt->execute([$recette_id]);
    
        // Supprimer la recette
        $stmt = $this->pdo->prepare("DELETE FROM recettes WHERE id = ?");
        $stmt->execute([$recette_id]);
    }
    
    // Récupérer toutes les recettes
    public function getRecettes() {
        $stmt = $this->pdo->query("SELECT * FROM recettes");
        return $stmt->fetchAll();
    }

    // Ajouter une recette
    public function ajouterRecette($name, $description, $portions, $preparation_time, $cooking_time) {
        $stmt = $this->pdo->prepare("
            INSERT INTO recettes (name, description, portions, preparation_time, cooking_time)
            VALUES (:name, :description, :portions, :preparation_time, :cooking_time)
        ");
        return $stmt->execute([
            ':name' => $name,
            ':description' => $description,
            ':portions' => $portions,
            ':preparation_time' => $preparation_time,
            ':cooking_time' => $cooking_time
        ]);
    }
    public function recetteRealisable($recette_id) {
        $stmt = $this->pdo->prepare("
            SELECT ir.name, ir.quantity, ir.unit, p.quantity AS stock_quantity
            FROM ingredients_recettes ir
            LEFT JOIN produits p ON ir.name = p.name
            WHERE ir.recette_id = :recette_id
        ");
        $stmt->execute([':recette_id' => $recette_id]);
        $ingredients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        // Vérifier si tous les ingrédients sont disponibles en stock
        foreach ($ingredients as $ing) {
            if ($ing['stock_quantity'] === null || $ing['stock_quantity'] < $ing['quantity']) {
                return false; // Manque d'ingrédients
            }
        }
    
        return true; // Tous les ingrédients sont disponibles
    }
    public function getRecette($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM recettes WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getIngredientsByRecette($recette_id) {
        $stmt = $this->pdo->prepare("
            SELECT ir.name, ir.quantity, ir.unit 
            FROM ingredients_recettes ir
            WHERE ir.recette_id = :recette_id
        ");
        $stmt->execute(['recette_id' => $recette_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function modifierRecette($id, $nom, $description, $portions, $preparation_time, $cooking_time) {
        $sql = "UPDATE recettes SET name = :nom, description = :description, portions = :portions, 
                preparation_time = :preparation_time, cooking_time = :cooking_time WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':nom' => $nom,
            ':description' => $description,
            ':portions' => $portions,
            ':preparation_time' => $preparation_time,
            ':cooking_time' => $cooking_time
        ]);
    }
    public function validerRecette($recette_id) {
        // Récupérer les ingrédients nécessaires pour la recette
        $stmt = $this->pdo->prepare("SELECT ir.name, ir.quantity FROM ingredients_recettes ir WHERE ir.recette_id = :recette_id");
        $stmt->execute(['recette_id' => $recette_id]);
        $ingredients = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($ingredients as $ing) {
            $stmt = $this->pdo->prepare("UPDATE produits SET quantity = quantity - :quantity WHERE name = :name AND quantity >= :quantity");
            $stmt->execute([
                'quantity' => $ing['quantity'],
                'name' => $ing['name']
            ]);
        }

        return true;
    }
    
    
    
    
}
?>
