<?php
session_start(); // Démarrer la session

// Vérifier si l'utilisateur est déjà authentifié ; s'il l'est, rediriger vers la page de profil
if (isset($_SESSION['user'])) {
    header("Location: profil.php");
    exit();
}

// Initialiser les variables
$nom = $email = $mot_de_passe = "";
$messageErreurInscription = "";
$messageErreurConnexion = "";

// Vérifier si le formulaire d'inscription a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['inscrire'])) {
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];

    // Validation des champs
    if (empty($nom) || empty($email) || empty($mot_de_passe)) {
        $messageErreurInscription = "Tous les champs sont obligatoires.";
    } else {
        // Vérifier si l'e-mail est déjà utilisé (adaptez le chemin du fichier selon vos besoins)
        $users = json_decode(file_get_contents('inscription.json'), true);
        $emailEnUsage = false;
        foreach ($users as $user) {
            if ($user['email'] === $email) {
                $emailEnUsage = true;
                break;
            }
        }

        if (!$emailEnUsage) {
            // Créer un nouvel utilisateur
            $nouvelUtilisateur = [
                "nom" => $nom,
                "email" => $email,
                "mot_de_passe" => password_hash($mot_de_passe, PASSWORD_BCRYPT)
            ];

            // Ajouter le nouvel utilisateur au tableau des utilisateurs
            $users[] = $nouvelUtilisateur;

            // Écrire le tableau mis à jour dans le fichier JSON
            file_put_contents('inscription.json', json_encode($users));

            // Initialiser la session et stocker les données de l'utilisateur dans la variable de session
            $_SESSION['user'] = $nouvelUtilisateur;
        } else {
            $messageErreurInscription = "Cet e-mail est déjà utilisé par un autre utilisateur.";
        }
    }
}

// Vérifier si le formulaire de connexion a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['connecter'])) {
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];

    // Validation des champs
    if (empty($email) || empty($mot_de_passe)) {
        $messageErreurConnexion = "Tous les champs sont obligatoires.";
    } else {
        // Vérifier si l'e-mail existe dans le fichier JSON et si le mot de passe correspond
        $users = json_decode(file_get_contents('inscription.json'), true);
        $utilisateurTrouve = null;
        foreach ($users as $user) {
            if ($user['email'] === $email && password_verify($mot_de_passe, $user['mot_de_passe'])) {
                $utilisateurTrouve = $user;
                break;
            }
        }

        if ($utilisateurTrouve) {
            // Initialiser la session et stocker les données de l'utilisateur dans la variable de session
            $_SESSION['user'] = $utilisateurTrouve;

            // Rediriger vers la page de profil après une connexion réussie
            header("Location: profil.php");
            exit();
        } else {
            $messageErreurConnexion = "Identifiants incorrects. Veuillez vérifier votre e-mail et votre mot de passe.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <?php require_once 'includes/head.php'; ?>
    <link rel="stylesheet" href="public/css/index.css">
    <title>FoodieShare - Inscription et Connexion</title>
</head>
<body>
    <div class="container">
        <?php require_once 'includes/header_inscription.php'; ?>
        <div class="content">
            <!-- Formulaire d'Inscription -->
            <form method="post">
                <fieldset>
                    <legend><b>Inscription</b></legend>
                    <label for="nom">Nom :</label>
                    <input type="text" name="nom" value="<?php echo $nom; ?>" required><br>

                    <label for="email">Adresse e-mail :</label>
                    <input type="email" name="email" value="<?php echo $email; ?>" required><br>

                    <label for="mot_de_passe">Mot de passe :</label>
                    <input type="password" name="mot_de_passe" required><br>

                    <input type="submit" name="inscrire" value="S'inscrire">
                </fieldset>
            </form>

            <!-- Message d'Erreur d'Inscription -->
            <?php
            if (!empty($messageErreurInscription)) {
                echo "<p>$messageErreurInscription</p>";
            }
            ?>
            <br>            
            <!-- Mesage "Déjà inscrit? Connectez-vous ci-dessous." en rouge -->
            <p style="color: red;"><b>Déjà inscrit ? Connectez-vous ci-dessous :</b> </p>
            <br>

            <!-- Formulaire de Connexion -->
            <form method="post">
                <fieldset>
                    <legend><b>Connexion</b></legend>
                    <label for="email_connexion">Adresse e-mail :</label>
                    <input type="email" name="email" required><br>

                    <label for="mot_de_passe_connexion">Mot de passe :</label>
                    <input type="password" name="mot_de_passe" required><br>

                    <input type="submit" name="connecter" value="Se connecter">
                </fieldset>
            </form>

            <!-- Message d'Erreur de Connexion -->
            <?php
            if (!empty($messageErreurConnexion)) {
                echo "<p>$messageErreurConnexion</p>";
            }
            ?>
        </div>
        <?php require_once 'includes/footer.php'; ?>
    </div>
</body>
</html>


