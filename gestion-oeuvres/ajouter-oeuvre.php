<?php
// Inclure le fichier de configuration de la base de données
require_once '../database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Nettoyer et valider les entrées utilisateur
    $nom_oeuvre = htmlspecialchars($_POST['nom_oeuvre'], ENT_QUOTES, 'UTF-8');
    $id_auteur = isset($_POST['id_auteur']) ? (int) $_POST['id_auteur'] : 0; // Conversion en entier

    // Vérifier si l'œuvre existe déjà en fonction du nom et de l'ID de l'auteur
    $oeuvreExiste = false;
    try {
        // Requête SQL pour vérifier l'existence de l'œuvre
        $sql = "SELECT id_oeuvre FROM OEUVRE WHERE nom_oeuvre = :nom_oeuvre AND id_auteur = :id_auteur";
        $stmt = $db->prepare($sql);

        // Lier les paramètres
        $stmt->bindParam(':nom_oeuvre', $nom_oeuvre, PDO::PARAM_STR);
        $stmt->bindParam(':id_auteur', $id_auteur, PDO::PARAM_INT);
        $stmt->execute();

        // Si l'œuvre existe déjà
        if ($stmt->rowCount() > 0) {
            $oeuvreExiste = true;
        }
    } catch (PDOException $e) {
        // Gérer les erreurs de base de données et sécuriser les messages d'erreur
        echo "Erreur lors de la vérification de l'œuvre : " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    }

    if (!$oeuvreExiste) {
        // L'œuvre n'existe pas, donc on peut l'ajouter
        try {
            // Préparez la requête d'insertion avec des paramètres liés
            $insertOeuvreSql = "INSERT INTO OEUVRE (nom_oeuvre, id_auteur) VALUES (:nom_oeuvre, :id_auteur)";
            $stmt = $db->prepare($insertOeuvreSql);

            // Lier les paramètres
            $stmt->bindParam(':nom_oeuvre', $nom_oeuvre, PDO::PARAM_STR);
            $stmt->bindParam(':id_auteur', $id_auteur, PDO::PARAM_INT);
            $stmt->execute();

            // Message de succès
            echo "Nouvelle œuvre ajoutée avec succès !";
            // Actualiser la liste des auteurs dans la fenêtre parente
            echo "<script>window.opener.location.reload();</script>";
        } catch (PDOException $e) {
            // Gérer les erreurs d'insertion et sécuriser les messages d'erreur
            echo "Erreur lors de l'ajout de l'œuvre : " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        }
    } else {
        // Message si l'œuvre existe déjà
        echo "Cette œuvre existe déjà en base de données.";
    }
}

// Récupérer la liste des auteurs existants pour le formulaire
try {
    // Requête pour récupérer les auteurs
    $sql = "SELECT id_auteur, CONCAT(prenom_auteur, ' ', nom_auteur) AS nom_complet FROM AUTEUR";
    $result = $db->query($sql);
    // Stocker les auteurs dans un tableau associatif
    $auteurs = $result->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Gérer les erreurs lors de la récupération des auteurs
    echo "Erreur lors de la récupération des auteurs : " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
}
// Afficher les erreurs en PHP
// (fonctionne à condition d’avoir activé l’option en local dans MAMP\conf\phpVersionPHP et modifier la ligne 374 de display_error = off en on)
error_reporting(E_ALL);
ini_set("display_errors", 1);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Ajouter une nouvelle œuvre</title>
</head>

<body>
    <h1>Ajouter une nouvelle œuvre</h1>
    <!-- Formulaire pour ajouter une nouvelle œuvre -->
    <form method="POST" action="ajouter-oeuvre.php">
        <label for="nom_oeuvre">Nom de l'œuvre:</label>
        <!-- Champ texte pour le nom de l'œuvre -->
        <input type="text" id="nom_oeuvre" name="nom_oeuvre" required><br>
        <label for="id_auteur">Auteur:</label>
        <!-- Sélecteur pour choisir l'auteur existant -->
        <select id="id_auteur" name="id_auteur" required>
            <?php foreach ($auteurs as $auteur) { ?>
                <option value="<?php echo htmlspecialchars($auteur['id_auteur'], ENT_QUOTES, 'UTF-8'); ?>">
                    <?php echo htmlspecialchars($auteur['nom_complet'], ENT_QUOTES, 'UTF-8'); ?>
                </option>
            <?php } ?>
        </select><br>
        <!-- Bouton pour soumettre le formulaire -->
        <input type="submit" value="Ajouter">
    </form>
</body>

</html>