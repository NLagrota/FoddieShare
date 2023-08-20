<?php
session_start(); // Démarrer la session

// Vérifier si l'utilisateur est déjà authentifié ; s'il l'est, rediriger vers la page de profil
if (isset($_SESSION['user'])) {
    header("Location: profil.php");
    exit();
}

// Initialiser les variables
$nom = $email = $mot_de_passe = "";
$messageErreur = "";

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['inscrire'])) {
        // Formulaire d'inscription soumis
        $nom = $_POST['nom'];
        $email = $_POST['email'];
        $mot_de_passe = $_POST['mot_de_passe'];

        // Valider les champs
        if (empty($nom) || empty($email) || empty($mot_de_passe)) {
            $messageErreur = "Tous les champs sont obligatoires.";
        } else {
            // Lire les données des utilisateurs depuis le fichier JSON
            $users = json_decode(file_get_contents('inscription.json'), true);

            // Vérifier si l'e-mail est déjà utilisé
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

                // Initialiser la variable $utilisateur avec les données du nouvel utilisateur
                $utilisateur = $nouvelUtilisateur;

                // Démarrer la session et stocker les données de l'utilisateur dans la variable de session
                session_start();
                $_SESSION['user'] = $utilisateur;

                // Rediriger l'utilisateur après une inscription réussie
                header("Location: profil.php");
                exit();
            } else {
                $messageErreur = "Cet e-mail est déjà utilisé par un autre utilisateur.";
            }
        }
    } elseif (isset($_POST['connecter'])) {
        // Formulaire de connexion soumis
        $email = $_POST['email'];
        $mot_de_passe = $_POST['mot_de_passe'];

        // Lire les données de l'utilisateur depuis le fichier JSON
        $users = json_decode(file_get_contents('inscription.json'), true);

        // Rechercher l'utilisateur avec l'e-mail fourni
        $utilisateurTrouve = null;
        foreach ($users as $user) {
            if ($user['email'] === $email) {
                $utilisateurTrouve = $user;
                break;
            }
        }

        if ($utilisateurTrouve && password_verify($mot_de_passe, $utilisateurTrouve['mot_de_passe'])) {
            // Initialiser la variable $utilisateur avec les données de l'utilisateur trouvé
            $utilisateur = $utilisateurTrouve;

            // Démarrer la session et stocker les données de l'utilisateur dans la variable de session
            session_start();
            $_SESSION['user'] = $utilisateur;

            // Rediriger l'utilisateur après une connexion réussie
            header("Location: profil.php");
            exit();
        } else {
            // Si les identifiants sont incorrects, afficher un message d'erreur
            $messageErreur = "Identifiants incorrects.";
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
            <!-- Formulaire d'inscription -->
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
            <br>
            <p>Déjà inscrit ? <a href="index.php">Connectez-vous ci-dessous :</a></p>
            <br>
            <!-- Formulaire de connexion -->
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
            
            <?php
            if (!empty($messageErreur)) {
                echo "<p>$messageErreur</p>";
            }
            ?>
        </div>
        <?php require_once 'includes/footer.php'; ?>
    </div>
</body>

</html>
