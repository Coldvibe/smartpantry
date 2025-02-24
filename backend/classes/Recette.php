<?php
require_once "Database.php";

class Recette {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getStockManquant($recette_id) {
        $stmt = $this->pdo->prepare("
            SELECT ir.name, ir.quantity, p.quantity AS stock_quantity
            FROM ingredients_recettes ir
            LEFT JOIN produits p ON ir.name = p.name
            WHERE ir.recette_id = :recette_id
        ");
        $stmt->execute(['recette_id' => $recette_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRecettes() {
        $stmt = $this->pdo->query("SELECT * FROM recettes");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRecette($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM recettes WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getIngredientsByRecette($recette_id) {
        $stmt = $this->pdo->prepare("
            SELECT ir.name, ir.quantity, ir.unit, 
                   COALESCE(p.quantity, 0) AS stock_quantity
            FROM ingredients_recettes ir
            LEFT JOIN produits p ON ir.name = p.name
            WHERE ir.recette_id = :recette_id
        ");
        $stmt->execute(['recette_id' => $recette_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function modifierRecette($id, $nom, $description, $portions, $preparation_time, $cooking_time, $imagePath = null) {
        $sql = "UPDATE recettes 
                SET name = :nom, description = :description, portions = :portions, 
                    preparation_time = :preparation_time, cooking_time = :cooking_time";
        
        // 📌 Si une nouvelle image est fournie, l'ajouter à la requête
        if ($imagePath) {
            $sql .= ", image = :imagePath";
        }

        $sql .= " WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        $params = [
            ':id' => $id,
            ':nom' => $nom,
            ':description' => $description,
            ':portions' => $portions,
            ':preparation_time' => $preparation_time,
            ':cooking_time' => $cooking_time
        ];

        if ($imagePath) {
            $params[':imagePath'] = $imagePath;
        }

        return $stmt->execute($params);
    }

    public function recetteRealisable($recette_id) {
        $ingredients = $this->getStockManquant($recette_id);
        foreach ($ingredients as $ing) {
            if ($ing['stock_quantity'] === null || $ing['stock_quantity'] < $ing['quantity']) {
                return false;
            }
        }
        return true;
    }

    public function validerRecette($recette_id) {
        $ingredients = $this->getStockManquant($recette_id);
        $this->pdo->beginTransaction();
        try {
            foreach ($ingredients as $ing) {
                $stmt = $this->pdo->prepare("
                    UPDATE produits SET quantity = quantity - :quantity 
                    WHERE name = :name AND quantity >= :quantity
                ");
                $stmt->execute([
                    'quantity' => $ing['quantity'],
                    'name' => $ing['name']
                ]);
                if ($stmt->rowCount() == 0) {
                    throw new Exception("Stock insuffisant pour " . $ing['name']);
                }
            }
            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            logError("Erreur validation recette: " . $e->getMessage());
            return false;
        }
    }

    public function ajouterRecette($name, $description, $portions, $preparation_time, $cooking_time, $imagePath = null) {
        $stmt = $this->pdo->prepare("
            INSERT INTO recettes (name, description, portions, preparation_time, cooking_time, image)
            VALUES (:name, :description, :portions, :preparation_time, :cooking_time, :image)
        ");
        $stmt->execute([
            ':name' => $name,
            ':description' => $description,
            ':portions' => $portions,
            ':preparation_time' => $preparation_time,
            ':cooking_time' => $cooking_time,
            ':image' => $imagePath
        ]);
    
        return $this->pdo->lastInsertId(); // Retourne l'ID de la dernière recette insérée
    }

    public function supprimerRecette($recette_id) {
        // 📌 Récupérer l'image de la recette avant suppression
        $stmt = $this->pdo->prepare("SELECT image FROM recettes WHERE id = ?");
        $stmt->execute([$recette_id]);
        $recette = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$recette) {
            return false;
        }

        // 📌 Supprimer l'image du dossier si elle existe
        if (!empty($recette['image']) && file_exists($recette['image'])) {
            unlink($recette['image']);
        }

        // 📌 Supprimer les ingrédients associés
        $stmt = $this->pdo->prepare("DELETE FROM ingredients_recettes WHERE recette_id = ?");
        $stmt->execute([$recette_id]);

        // 📌 Supprimer la recette
        $stmt = $this->pdo->prepare("DELETE FROM recettes WHERE id = ?");
        return $stmt->execute([$recette_id]);
    }
}
?>
