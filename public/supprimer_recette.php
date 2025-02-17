<?php
require_once "../backend/classes/Recette.php";

if (isset($_GET['id'])) {
    $recetteObj = new Recette();
    $recetteObj->supprimerRecette($_GET['id']);
}

header("Location: recettes.php");
exit;
