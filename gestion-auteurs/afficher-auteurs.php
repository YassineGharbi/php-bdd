<!DOCTYPE html>
<html>

<head>
    <title>Liste des Auteurs</title>
</head>

<body>
    <?php
    // Inclure le fichier de configuration de la base de données
    require_once '../database.php';

    try {
        // Requête SQL pour récupérer la liste des auteurs
        $requete = "SELECT id_auteur, prenom_auteur, nom_auteur FROM AUTEUR";
        $resultat = $db->query($requete);

        // Affichage de la liste des auteurs avec les boutons "Modifier" et "Supprimer"
        echo "<h1>Liste des auteurs</h1>";
        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<tr><th>ID</th><th>Prénom</th><th>Nom</th><th>Modifier</th><th>Supprimer</th></tr>";

        while ($auteur = $resultat->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>{$auteur['id_auteur']}</td>";
            echo "<td>{$auteur['prenom_auteur']}</td>";
            echo "<td>{$auteur['nom_auteur']}</td>";
            // Bouton "Modifier" qui ouvre une fenêtre pop-up pour modifier l'auteur
            echo "<td><button onclick='modifierAuteur({$auteur['id_auteur']})'>Modifier</button></td>";
            // Bouton "Supprimer" avec confirmation
            echo "<td><button onclick='confirmationSuppAuteur({$auteur['id_auteur']})'>Supprimer</button></td>";
            echo "</tr>";
        }

        echo "</table>";
    } catch (PDOException $e) {
        die("Erreur lors de la récupération des auteurs : " . $e->getMessage());
    }
    // Afficher les erreurs en PHP
// (fonctionne à condition d’avoir activé l’option en local dans MAMP\conf\phpVersionPHP et modifier la ligne 374 de display_error = off en on)
    error_reporting(E_ALL);
    ini_set("display_errors", 1);
    ?>

    <!-- Bouton pour ajouter un auteur dans une petite fenêtre -->
    <br>
    <button onclick="ajouterAuteur()">Ajouter un auteur</button>

    <!-- Bouton pour renvoyer vers la liste des œuvres -->
    <button onclick="window.location.href='../gestion-oeuvres/afficher-oeuvres.php'">Afficher les œuvres</button>

    <!-- Scripts JavaScript pour les fenêtres pop-up et la suppression -->
    <script>
        function modifierAuteur(id_auteur) {
            // Ouvrir une fenêtre pop-up avec le formulaire de modification de l'auteur
            var popupWindow = window.open('modifier-auteur.php?id=' + id_auteur, 'Modifier Auteur', 'width=400,height=300');
        }

        function confirmationSuppAuteur(id_auteur) {
            // Demander une confirmation avant de supprimer un auteur et toutes ses œuvres associées
            if (confirm("Êtes-vous sûr de vouloir supprimer cet auteur ?")) {
                // Rediriger vers la page de suppression avec l'ID de l'auteur
                window.location.href = 'supprimer-auteur.php?id_auteur=' + id_auteur;
            }
        }

        function ajouterAuteur() {
            // Ouvrir une fenêtre pop-up pour ajouter un auteur
            var popupWindow = window.open('ajouter-auteur.php', 'Ajouter Auteur', 'width=400,height=300');
        }
    </script>
</body>

</html>