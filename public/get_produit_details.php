<?php
require_once "../backend/classes/Database.php";

$pdo = Database::getInstance()->getConnection();

if (isset($_POST['nom'])) {
    $nom = trim($_POST['nom']);
    $stmt = $pdo->prepare("SELECT unite FROM ciqual_aliments WHERE nom_aliment = :nom LIMIT 1");
    $stmt->execute(['nom' => $nom]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        echo json_encode(["unite" => $result["unite"]]);
    } else {
        echo json_encode(["unite" => ""]);
    }
}
?>
