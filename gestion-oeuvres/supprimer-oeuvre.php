<?php
// Inclusion du fichier de connexion à la base de données
require '../database.php';

// Vérifiez si l'ID de l'œuvre est présent dans la requête GET
if (isset($_GET['id_oeuvre'])) {
    // Récupérez l'ID de l'œuvre à supprimer depuis la requête GET
    $id_oeuvre = htmlspecialchars($_GET['id_oeuvre'], ENT_QUOTES, 'UTF-8');

    // Validation de l'ID pour s'assurer qu'il s'agit d'un entier
    if (!filter_var($id_oeuvre, FILTER_VALIDATE_INT)) {
        die('ID de l\'œuvre non valide.');
    }

    try {
        // Commencez une transaction pour garantir l'intégrité des données
        $db->beginTransaction();

        // Préparez la requête SQL pour supprimer l'œuvre en utilisant un paramètre préparé
        $sql = "DELETE FROM OEUVRE WHERE id_oeuvre = :id_oeuvre";
        $stmt = $db->prepare($sql);

        // Liez le paramètre et exécutez la requête
        $stmt->bindParam(':id_oeuvre', $id_oeuvre, PDO::PARAM_INT);
        $stmt->execute();

        // Validez la transaction
        $db->commit();

        // Redirigez vers la page de la liste des œuvres après la suppression
        header('Location: afficher-oeuvres.php');
        // Ajout de exit() pour éviter d'exécuter du code après la redirection
        exit();
    } catch (PDOException $e) {
        // En cas d'erreur, annulez la transaction et affichez un message d'erreur
        $db->rollBack();
        echo 'Erreur lors de la suppression de l\'œuvre : ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    }
} else {
    // Gérer une erreur si l'ID de l'œuvre n'est pas défini dans l'URL
    echo 'ID de l\'œuvre non spécifié.';
}
// Afficher les erreurs en PHP
// (fonctionne à condition d’avoir activé l’option en local dans MAMP\conf\phpVersionPHP et modifier la ligne 374 de display_error = off en on)
error_reporting(E_ALL);
ini_set("display_errors", 1);
?>