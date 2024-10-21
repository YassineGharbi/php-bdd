<?php
// Inclure le fichier de configuration de la base de données pour se connecter
require_once '../database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Récupération des données du formulaire en utilisant htmlspecialchars pour prévenir les injections XSS
        $id_auteur = htmlspecialchars($_POST['id_auteur'], ENT_QUOTES, 'UTF-8');
        $nouveau_prenom = htmlspecialchars($_POST['nouveau_prenom'], ENT_QUOTES, 'UTF-8');
        $nouveau_nom = htmlspecialchars($_POST['nouveau_nom'], ENT_QUOTES, 'UTF-8');

        // Vérification que l'ID de l'auteur est un entier valide
        if (!filter_var($id_auteur, FILTER_VALIDATE_INT)) {
            throw new Exception("ID de l'auteur invalide.");
        }

        // Vérification si le nouvel auteur (prénom et nom) existe déjà dans la base de données
        $requete_verification = "SELECT COUNT(*) FROM AUTEUR WHERE prenom_auteur = :prenom AND nom_auteur = :nom AND id_auteur != :id";
        $stmt_verification = $db->prepare($requete_verification);
        $stmt_verification->bindParam(':prenom', $nouveau_prenom, PDO::PARAM_STR);
        $stmt_verification->bindParam(':nom', $nouveau_nom, PDO::PARAM_STR);
        $stmt_verification->bindParam(':id', $id_auteur, PDO::PARAM_INT);
        $stmt_verification->execute();
        $existe_deja = $stmt_verification->fetchColumn();

        // Si un auteur avec ce nom et prénom existe déjà, on renvoie une erreur
        if ($existe_deja) {
            throw new Exception("Un auteur avec ce prénom et ce nom existe déjà.");
        }

        // Mise à jour de l'auteur avec les nouveaux prénom et nom
        $requete = "UPDATE AUTEUR SET prenom_auteur = :prenom, nom_auteur = :nom WHERE id_auteur = :id";
        $stmt = $db->prepare($requete);
        $stmt->bindParam(':id', $id_auteur, PDO::PARAM_INT);
        $stmt->bindParam(':prenom', $nouveau_prenom, PDO::PARAM_STR);
        $stmt->bindParam(':nom', $nouveau_nom, PDO::PARAM_STR);

        // Exécution de la requête
        $stmt->execute();

        // Fermer la fenêtre et actualiser la liste des auteurs dans la fenêtre parente après la modification
        echo "<script>window.close(); window.opener.location.reload();</script>";
    } catch (PDOException $e) {
        // En cas d'erreur SQL, afficher un message d'erreur sécurisé
        die("Erreur lors de la mise à jour de l'auteur : " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
    } catch (Exception $e) {
        // Gérer les autres exceptions et afficher le message d'erreur
        die("Erreur : " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
    }
} else {
    // Si la méthode n'est pas POST, récupérer l'ID de l'auteur via la méthode GET pour pré-remplir le formulaire
    $id_auteur = htmlspecialchars($_GET['id'], ENT_QUOTES, 'UTF-8');

    // Vérification que l'ID est bien un entier
    if (!filter_var($id_auteur, FILTER_VALIDATE_INT)) {
        die("ID de l'auteur invalide.");
    }

    // Requête pour récupérer les informations actuelles de l'auteur
    try {
        $requete_info_auteur = "SELECT prenom_auteur, nom_auteur FROM AUTEUR WHERE id_auteur = :id";
        $stmt_info_auteur = $db->prepare($requete_info_auteur);
        $stmt_info_auteur->bindParam(':id', $id_auteur, PDO::PARAM_INT);
        $stmt_info_auteur->execute();
        $info_auteur = $stmt_info_auteur->fetch(PDO::FETCH_ASSOC);

        // Si aucun auteur n'est trouvé avec cet ID, on renvoie un message d'erreur
        if (!$info_auteur) {
            die("Aucun auteur trouvé avec cet ID.");
        }
    } catch (PDOException $e) {
        // En cas d'erreur lors de la récupération des informations de l'auteur, afficher un message d'erreur sécurisé
        die("Erreur lors de la récupération des informations de l'auteur : " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
    }
}

// Afficher les erreurs PHP pour débogage
error_reporting(E_ALL);
ini_set("display_errors", 1);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Modifier Auteur</title>
</head>

<body>
    <h1>Modifier l'auteur</h1>

    <!-- Formulaire de modification de l'auteur -->
    <form method="post" action="modifier-auteur.php">
        <!-- Champ caché pour l'ID de l'auteur -->
        <input type="hidden" name="id_auteur" value="<?= htmlspecialchars($id_auteur, ENT_QUOTES, 'UTF-8') ?>">

        <!-- Champ pour le nouveau prénom de l'auteur -->
        <label for="nouveau_prenom">Nouveau prénom :</label>
        <input type="text" name="nouveau_prenom" id="nouveau_prenom"
            value="<?= htmlspecialchars($info_auteur['prenom_auteur'], ENT_QUOTES, 'UTF-8') ?>" required><br>

        <!-- Champ pour le nouveau nom de l'auteur -->
        <label for="nouveau_nom">Nouveau nom :</label>
        <input type="text" name="nouveau_nom" id="nouveau_nom"
            value="<?= htmlspecialchars($info_auteur['nom_auteur'], ENT_QUOTES, 'UTF-8') ?>" required><br>

        <!-- Section des boutons alignés horizontalement -->
        <div style="display: flex; gap: 10px; margin-top: 20px;">
            <!-- Bouton Modifier pour soumettre le formulaire -->
            <input type="submit" value="Modifier">

            <!-- Bouton Fermer pour fermer la fenêtre sans modifier -->
            <button type="button" onclick="window.close();">Fermer</button>
        </div>
    </form>
</body>

</html>
