<?php
// Inclure le fichier de configuration de la base de données
require_once '../database.php';


// Initialiser une variable pour afficher les messages
$message = '';


// Vérifiez si la méthode de requête est POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Nettoyer et valider les données de formulaire
    $prenom_auteur = htmlspecialchars($_POST['prenom_auteur'], ENT_QUOTES, 'UTF-8');
    $nom_auteur = htmlspecialchars($_POST['nom_auteur'], ENT_QUOTES, 'UTF-8');


    // Vérifier si l'auteur existe déjà en fonction du prénom et du nom
    $auteurExiste = false;
    try {
        // Requête SQL pour vérifier l'existence de l'auteur
        $sql = "SELECT id_auteur FROM AUTEUR WHERE prenom_auteur = :prenom_auteur AND nom_auteur = :nom_auteur";
        $stmt = $db->prepare($sql);


        // Lier les paramètres avec les valeurs nettoyées
        $stmt->bindParam(':prenom_auteur', $prenom_auteur, PDO::PARAM_STR);
        $stmt->bindParam(':nom_auteur', $nom_auteur, PDO::PARAM_STR);
        $stmt->execute();


        // Si l'auteur existe déjà
        if ($stmt->rowCount() > 0) {
            $auteurExiste = true;
        }
    } catch (PDOException $e) {
        // Gérer les erreurs de base de données et sécuriser le message d'erreur
        $message = "Erreur lors de la vérification de l'auteur : " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    }


    if (!$auteurExiste) {
        // L'auteur n'existe pas, donc on peut l'ajouter
        try {
            // Préparez la requête d'insertion avec des paramètres liés
            $insertAuteurSql = "INSERT INTO AUTEUR (prenom_auteur, nom_auteur) VALUES (:prenom_auteur, :nom_auteur)";
            $stmt = $db->prepare($insertAuteurSql);


            // Lier les paramètres
            $stmt->bindParam(':prenom_auteur', $prenom_auteur, PDO::PARAM_STR);
            $stmt->bindParam(':nom_auteur', $nom_auteur, PDO::PARAM_STR);
            $stmt->execute();


            // Message de succès
            $message = "Nouvel auteur ajouté avec succès !";
            // Actualiser la liste des auteurs dans la fenêtre parente
            echo "<script>window.opener.location.reload();</script>";
        } catch (PDOException $e) {
            // Gérer les erreurs d'insertion et sécuriser le message d'erreur
            $message = "Erreur lors de l'ajout de l'auteur : " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        }
    } else {
        // L'auteur existe déjà
        $message = "Cet auteur existe déjà en base de données.";
    }
}
// Afficher les erreurs en PHP
// (fonctionne à condition d’avoir activé l’option en local dans MAMP\conf\phpVersionPHP et modifier la ligne 374 de display_error = off en on)
error_reporting(E_ALL);
ini_set("display_errors", 1);
?>


<!DOCTYPE html>
<html>

<head>
    <title>Ajouter un nouvel auteur</title>
</head>

<body>
    <h1>Ajouter un nouvel auteur</h1>
    <!-- Affichage du message d'état -->
    <p><?php echo $message; ?></p>


    <!-- Formulaire d'ajout d'un nouvel auteur -->
    <form method="POST" action="ajouter-auteur.php">
        <label for="prenom_auteur">Prénom de l'auteur:</label>
        <input type="text" id="prenom_auteur" name="prenom_auteur" required><br>
        <label for="nom_auteur">Nom de l'auteur:</label>
        <input type="text" id="nom_auteur" name="nom_auteur" required><br>
        <!-- Bouton pour soumettre le formulaire -->
        <input type="submit" value="Ajouter">
    </form>
</body>

</html>