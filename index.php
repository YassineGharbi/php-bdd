<?php
// Inclusion du fichier de configuration de la base de données si nécessaire
// include 'database.php'; // Décommenter si besoin d'accès à la DB
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link rel="stylesheet" href="style.css"> <!-- Si vous avez un fichier CSS -->
</head>
<body>
    <header>
        <h1>Bienvenue dans le Gestionnaire d'Œuvres</h1>
    </header>
    
    <nav>
        <ul>
            <li><a href="./gestion-auteurs/afficher-auteurs.php">Voir les Auteurs</a></li>
            <li><a href="./gestion-oeuvres/afficher-oeuvres.php">Voir les Œuvres</a></li>
        </ul>
    </nav>

    <main>
        <h2>Navigation</h2>
        <p>Utilisez les liens ci-dessus pour naviguer entre les auteurs et les œuvres.</p>
    </main>

    <footer>
        <p>&copy; 2024 Gestionnaire d'Œuvres</p>
    </footer>
</body>
</html>