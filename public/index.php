<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartPantry - Accueil</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php require_once 'navbar.php'; ?>


<div class="container mt-5">
    <div class="hero-section">
        <h1>Bienvenue sur <span class="highlight">SmartPantry</span></h1>
        <p>Gérez votre stock de nourriture et vos recettes intelligemment.</p>
        <a href="recettes.php" class="btn btn-primary">📖 Voir les Recettes</a>
        <a href="stock.php" class="btn btn-secondary">📦 Voir le Stock</a>
    </div>

    <!-- Sections -->
    <div class="row mt-5">
        <div class="col-md-6">
            <div class="feature-card">
                <h3>📊 Gestion du Stock</h3>
                <p>Ajoutez, modifiez et suivez l’état de vos produits alimentaires.</p>
                <a href="stock.php" class="btn btn-outline-primary">Gérer mon Stock</a>
            </div>
        </div>
        <div class="col-md-6">
            <div class="feature-card">
                <h3>🍽️ Planification des Recettes</h3>
                <p>Créez et suivez vos recettes en fonction de votre stock disponible.</p>
                <a href="recettes.php" class="btn btn-outline-primary">Voir les Recettes</a>
            </div>
        </div>
    </div>
</div>


<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
