<nav class="navbar navbar-expand-lg custom-navbar">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand" href="index.php">
            <img src="assets/images/logo.png" alt="SmartPantry Logo" height="40">
            <span class="brand-text">SmartPantry</span>
        </a>

        <!-- Bouton mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menu -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="recettes.php">🍽️ Recettes</a></li>
                <li class="nav-item"><a class="nav-link" href="stock.php">📦 Stock</a></li>
                <li class="nav-item"><a class="nav-link" href="liste_courses.php">🛒 Liste de courses</a></li>
            </ul>

            <!-- Switch Mode Clair/Sombre -->
            <div class="form-check form-switch ms-3">
                <input class="form-check-input" type="checkbox" id="darkModeSwitch">
                <label class="form-check-label" for="darkModeSwitch">🌙 Mode sombre</label>
            </div>
        </div>
    </div>
</nav>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const darkModeSwitch = document.getElementById("darkModeSwitch");
        const body = document.body;

        // Vérifier si le mode sombre est activé
        if (localStorage.getItem("darkMode") === "enabled") {
            body.classList.add("dark-mode");
            darkModeSwitch.checked = true;
        }

        darkModeSwitch.addEventListener("change", function () {
            if (this.checked) {
                body.classList.add("dark-mode");
                localStorage.setItem("darkMode", "enabled");
            } else {
                body.classList.remove("dark-mode");
                localStorage.setItem("darkMode", "disabled");
            }
        });
    });
</script>
