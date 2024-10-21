<?php
// Inclure le fichier de configuration de la base de données
require_once '../database.php';

$error = '';  // Variable pour stocker les messages d'erreur
$info_oeuvre = null;  // Initialiser la variable pour éviter les erreurs si aucune œuvre n'est trouvée

// Charger la liste des auteurs dès le début, car elle est nécessaire pour afficher le formulaire
try {
    $requete_auteurs = "SELECT id_auteur, prenom_auteur, nom_auteur FROM AUTEUR";
    $resultat_auteurs = $db->query($requete_auteurs); // Exécute la requête pour récupérer les auteurs
} catch (PDOException $e) {
    // Gérer les erreurs de base de données pour éviter les affichages d'erreur brut
    die("Erreur lors de la récupération des auteurs : " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Récupérer et sécuriser les données du formulaire
        $id_oeuvre = htmlspecialchars($_POST['id_oeuvre'], ENT_QUOTES, 'UTF-8');
        $nouveau_titre = htmlspecialchars($_POST['nouveau_titre'], ENT_QUOTES, 'UTF-8');
        $nouvel_id_auteur = htmlspecialchars($_POST['nouvel_id_auteur'], ENT_QUOTES, 'UTF-8');

        // Valider que les IDs sont bien des entiers
        if (!filter_var($id_oeuvre, FILTER_VALIDATE_INT) || !filter_var($nouvel_id_auteur, FILTER_VALIDATE_INT)) {
            throw new Exception("ID invalide pour l'œuvre ou l'auteur.");
        }

        // Vérifier si une œuvre avec le même titre et auteur existe déjà
        $requete_verif = "SELECT COUNT(*) FROM OEUVRE WHERE nom_oeuvre = :titre AND id_auteur = :id_auteur AND id_oeuvre != :id";
        $stmt_verif = $db->prepare($requete_verif);
        $stmt_verif->bindParam(':titre', $nouveau_titre, PDO::PARAM_STR);
        $stmt_verif->bindParam(':id_auteur', $nouvel_id_auteur, PDO::PARAM_INT);
        $stmt_verif->bindParam(':id', $id_oeuvre, PDO::PARAM_INT);
        $stmt_verif->execute();

        // Si une œuvre existe déjà, renvoyer une erreur
        if ($stmt_verif->fetchColumn() > 0) {
            throw new Exception("Une œuvre avec ce titre et cet auteur existe déjà.");
        }

        // Mettre à jour l'œuvre dans la base de données
        $requete = "UPDATE OEUVRE SET nom_oeuvre = :titre, id_auteur = :id_auteur WHERE id_oeuvre = :id";
        $stmt = $db->prepare($requete);
        $stmt->bindParam(':id', $id_oeuvre, PDO::PARAM_INT);
        $stmt->bindParam(':titre', $nouveau_titre, PDO::PARAM_STR);
        $stmt->bindParam(':id_auteur', $nouvel_id_auteur, PDO::PARAM_INT);

        // Exécuter la requête de mise à jour
        $stmt->execute();

        // Rafraîchir la fenêtre parente et fermer la fenêtre actuelle
        echo "<script>window.opener.location.reload(); window.close();</script>";
        exit(); 
    } catch (PDOException $e) {
        // Gestion des erreurs SQL
        $error = "Erreur lors de la mise à jour de l'œuvre : " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    } catch (Exception $e) {
        // Gestion d'autres types d'erreurs
        $error = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    }

    // Si une erreur est survenue, recharger les informations de l'œuvre
    try {
        $requete_info_oeuvre = "SELECT id_oeuvre, nom_oeuvre, id_auteur FROM OEUVRE WHERE id_oeuvre = :id";
        $stmt_info_oeuvre = $db->prepare($requete_info_oeuvre);
        $stmt_info_oeuvre->bindParam(':id', $id_oeuvre, PDO::PARAM_INT);
        $stmt_info_oeuvre->execute();
        $info_oeuvre = $stmt_info_oeuvre->fetch(PDO::FETCH_ASSOC);

        // Vérifier si l'œuvre existe
        if (!$info_oeuvre) {
            die("Aucune œuvre trouvée avec cet ID.");
        }
    } catch (PDOException $e) {
        // Gestion d'erreur lors de la récupération des informations
        $error = "Erreur lors de la récupération des informations : " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    }

    // Recharger la liste des auteurs en cas d'erreur pour que le formulaire soit complet
    try {
        $requete_auteurs = "SELECT id_auteur, prenom_auteur, nom_auteur FROM AUTEUR";
        $resultat_auteurs = $db->query($requete_auteurs); // Recharger la liste des auteurs
    } catch (PDOException $e) {
        die("Erreur lors de la récupération des auteurs : " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
    }
} else {
    // Requête GET pour charger les données de l'œuvre à modifier
    $id_oeuvre = htmlspecialchars($_GET['id'], ENT_QUOTES, 'UTF-8');

    // Valider l'ID de l'œuvre pour éviter des problèmes de type
    if (!filter_var($id_oeuvre, FILTER_VALIDATE_INT)) {
        die("ID de l'œuvre invalide.");
    }

    // Récupérer les informations actuelles de l'œuvre
    try {
        $requete_info_oeuvre = "SELECT id_oeuvre, nom_oeuvre, id_auteur FROM OEUVRE WHERE id_oeuvre = :id";
        $stmt_info_oeuvre = $db->prepare($requete_info_oeuvre);
        $stmt_info_oeuvre->bindParam(':id', $id_oeuvre, PDO::PARAM_INT);
        $stmt_info_oeuvre->execute();
        $info_oeuvre = $stmt_info_oeuvre->fetch(PDO::FETCH_ASSOC);

        // Vérifier si l'œuvre existe
        if (!$info_oeuvre) {
            die("Aucune œuvre trouvée avec cet ID.");
        }
    } catch (PDOException $e) {
        die("Erreur lors de la récupération des informations : " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
    }
}

// Afficher les erreurs PHP pour faciliter le débogage (à éviter en production)
error_reporting(E_ALL);
ini_set("display_errors", 1);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Modifier Œuvre</title>
</head>

<body>
    <h1>Modifier l'œuvre</h1>
    
    <!-- Afficher un message d'erreur s'il existe -->
    <?php if (!empty($error)): ?>
        <p style="color:red;"><?= $error ?></p>
    <?php endif; ?>
    
    <!-- Formulaire de modification de l'œuvre -->
    <form method="post" action="modifier-oeuvre.php">
        <!-- Champ caché pour l'ID de l'œuvre (non modifiable par l'utilisateur) -->
        <input type="hidden" name="id_oeuvre" value="<?= htmlspecialchars($info_oeuvre['id_oeuvre'], ENT_QUOTES, 'UTF-8') ?>">

        <!-- Champ pour le nouveau titre de l'œuvre, pré-rempli avec l'ancien titre -->
        <label for="nouveau_titre">Nouveau titre :</label>
        <input type="text" name="nouveau_titre" id="nouveau_titre"
            value="<?= isset($_POST['nouveau_titre']) ? htmlspecialchars($_POST['nouveau_titre'], ENT_QUOTES, 'UTF-8') : htmlspecialchars($info_oeuvre['nom_oeuvre'], ENT_QUOTES, 'UTF-8') ?>" required><br>

        <!-- Liste déroulante pour choisir le nouvel auteur, avec sélection de l'auteur actuel -->
        <label for="nouvel_id_auteur">Nouvel auteur :</label>
        <select name="nouvel_id_auteur" id="nouvel_id_auteur" required>
            <?php while ($auteur = $resultat_auteurs->fetch(PDO::FETCH_ASSOC)): ?>
                <option value="<?= htmlspecialchars($auteur['id_auteur'], ENT_QUOTES, 'UTF-8') ?>"
                    <?= (isset($_POST['nouvel_id_auteur']) && $_POST['nouvel_id_auteur'] == $auteur['id_auteur']) || ($info_oeuvre['id_auteur'] == $auteur['id_auteur']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($auteur['prenom_auteur'] . ' ' . $auteur['nom_auteur'], ENT_QUOTES, 'UTF-8') ?>
                </option>
            <?php endwhile; ?>
        </select><br>

        <!-- Ajouter un conteneur pour les boutons alignés horizontalement -->
        <div style="display: flex; gap: 10px; margin-top: 20px;">
            <!-- Bouton Modifier -->
            <input type="submit" value="Modifier">

            <!-- Bouton de fermeture -->
            <button type="button" onclick="window.close();">Fermer</button>
        </div>
    </form>
</body>

</html>
