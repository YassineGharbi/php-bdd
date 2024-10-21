<?php
// Inclusion du fichier de connexion à la base de données
require '../database.php';

if (isset($_GET['id_auteur'])) {
    try {
        // Récupérer et valider l'ID de l'auteur
        $id_auteur = htmlspecialchars($_GET['id_auteur'], ENT_QUOTES, 'UTF-8');

        // Vérifier si l'ID est valide
        if (!filter_var($id_auteur, FILTER_VALIDATE_INT)) {
            throw new Exception("ID de l'auteur invalide.");
        }

        // Commencer une transaction
        $db->beginTransaction();

        // Vérifier si l'auteur a des œuvres associées
        $sql_check_oeuvres = "SELECT COUNT(*) FROM OEUVRE WHERE id_auteur = :id_auteur";
        $stmt_check_oeuvres = $db->prepare($sql_check_oeuvres);
        $stmt_check_oeuvres->bindParam(':id_auteur', $id_auteur, PDO::PARAM_INT);
        $stmt_check_oeuvres->execute();
        $oeuvres_count = $stmt_check_oeuvres->fetchColumn();

        // Si l'auteur a des œuvres, on empêche la suppression
        if ($oeuvres_count > 0) {
            // Lancer un message d'erreur en pop-up
            echo "<script>alert('Impossible de supprimer l\'auteur. Il a encore des œuvres associées.'); window.location.href='afficher-auteurs.php';</script>";
            exit();
        }

        // Supprimer l'auteur uniquement s'il n'a pas d'œuvres associées
        $sql_delete_auteur = "DELETE FROM AUTEUR WHERE id_auteur = :id_auteur";
        $stmt_delete_auteur = $db->prepare($sql_delete_auteur);
        $stmt_delete_auteur->bindParam(':id_auteur', $id_auteur, PDO::PARAM_INT);
        $stmt_delete_auteur->execute();

        // Valider la transaction
        $db->commit();

        // Redirection vers la page des auteurs après la suppression
        header('Location: afficher-auteurs.php');
        exit();
    } catch (PDOException $e) {
        // En cas d'erreur, annuler la transaction et afficher un message d'erreur sécurisé
        $db->rollBack();
        echo "<script>alert('Erreur lors de la suppression : " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "'); window.location.href='afficher-auteurs.php';</script>";
        exit();
    } catch (Exception $e) {
        // Gérer les autres erreurs
        echo "<script>alert('Erreur : " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "'); window.location.href='afficher-auteurs.php';</script>";
        exit();
    }
} else {
    // Gestion de l'erreur si l'ID de l'auteur n'est pas défini
    echo "<script>alert('ID de l\'auteur non spécifié.'); window.location.href='afficher-auteurs.php';</script>";
    exit();
}

// Afficher les erreurs en PHP
error_reporting(E_ALL);
ini_set("display_errors", 1);
?>