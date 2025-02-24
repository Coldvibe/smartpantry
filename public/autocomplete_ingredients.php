<?php
require_once "../backend/classes/Database.php";

$pdo = Database::getInstance()->getConnection();

if (isset($_POST['query'])) {
    $search = trim($_POST['query']);

    // Sélection des ingrédients en 2 étapes :
    // 1️⃣ Ceux qui commencent par le mot recherché
    // 2️⃣ Ceux qui contiennent le mot recherché mais ne commencent pas par lui

    $stmt = $pdo->prepare("
        (SELECT nom_aliment FROM ciqual_aliments WHERE nom_aliment LIKE :start_query ORDER BY nom_aliment LIMIT 5)
        UNION 
        (SELECT nom_aliment FROM ciqual_aliments WHERE nom_aliment LIKE :any_query AND nom_aliment NOT LIKE :start_query ORDER BY nom_aliment LIMIT 10)
    ");

    $stmt->execute([
        ':start_query' => $search . '%',
        ':any_query' => '%' . $search . '%'
    ]);

    $results = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if ($results) {
        foreach ($results as $row) {
            echo "<div class='suggestion-item p-2 border-bottom'>" . htmlspecialchars($row) . "</div>";
        }
    } else {
        echo "<div class='suggestion-item p-2 text-muted'>Aucun résultat</div>";
    }
}
?>
