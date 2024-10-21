<!DOCTYPE html>
<html>

<head>
    <title>Liste des Œuvres</title>
</head>

<body>
    <?php
    // Inclure le fichier de configuration de la base de données
    require_once '../database.php';


    try {
        // Requête SQL pour récupérer la liste des œuvres avec les noms des auteurs
        $requete = "SELECT O.id_oeuvre, O.nom_oeuvre, A.prenom_auteur, A.nom_auteur 
                    FROM OEUVRE O 
                    INNER JOIN AUTEUR A ON O.id_auteur = A.id_auteur";
        $resultat = $db->query($requete);


        // Affichage de la liste des œuvres avec les boutons "Modifier" et "Supprimer"
        echo "<h1>Liste des œuvres</h1>";
        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<tr><th>ID</th><th>Titre de l'Œuvre</th><th>Auteur</th><th>Modifier</th><th>Supprimer</th></tr>";


        while ($oeuvre = $resultat->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>{$oeuvre['id_oeuvre']}</td>";
            echo "<td>{$oeuvre['nom_oeuvre']}</td>";
            echo "<td>{$oeuvre['prenom_auteur']} {$oeuvre['nom_auteur']}</td>";
            // Bouton "Modifier" qui ouvre une fenêtre pop-up pour modifier l'œuvre
            echo "<td><button onclick='modifierOeuvre({$oeuvre['id_oeuvre']})'>Modifier</button></td>";
            // Bouton "Supprimer" avec confirmation
            echo "<td><button onclick='confirmationSuppOeuvre({$oeuvre['id_oeuvre']})'>Supprimer</button></td>";
            echo "</tr>";
        }


        echo "</table>";
    } catch (PDOException $e) {
        die("Erreur lors de la récupération des œuvres : " . $e->getMessage());
    }
    // Afficher les erreurs en PHP
// (fonctionne à condition d’avoir activé l’option en local dans MAMP\conf\phpVersionPHP et modifier la ligne 374 de display_error = off en on)
    error_reporting(E_ALL);
    ini_set("display_errors", 1);
    ?>


    <!-- Bouton pour ajouter une œuvre dans une petite fenêtre -->
    <br>
    <button onclick="ajouterOeuvre()">Ajouter une œuvre</button>


    <!-- Bouton pour renvoyer vers la liste des auteurs -->
    <button onclick="window.location.href='../gestion-auteurs/afficher-auteurs.php'">Afficher les auteurs</button>


    <!-- Script JavaScript pour gérer l'ajout, la modification et la suppression -->
    <script>
        function ajouterOeuvre() {
            // Ouvrir une fenêtre pop-up pour ajouter une œuvre
            var popupWindow = window.open('ajouter-oeuvre.php', 'Ajouter Œuvre', 'width=400,height=300');
        }


        function modifierOeuvre(id_oeuvre) {
            // Ouvrir une fenêtre pop-up avec le formulaire de modification de l'œuvre
            var popupWindow = window.open('modifier-oeuvre.php?id=' + id_oeuvre, 'Modifier Œuvre', 'width=400,height=300');
        }


        function confirmationSuppOeuvre(id_oeuvre) {
            // Demander une confirmation avant de supprimer une œuvre
            if (confirm("Êtes-vous sûr de vouloir supprimer cette œuvre ?")) {
                // Rediriger vers la page de suppression avec l'ID de l'œuvre à supprimer
                window.location.href = 'supprimer-oeuvre.php?id_oeuvre=' + id_oeuvre;
            }
        }
    </script>
</body>

</html>