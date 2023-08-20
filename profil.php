<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: connexion.php");
    exit();
}

$utilisateur = $_SESSION['user'];

// Vérifiez le chemin du fichier JSON

$filename = __DIR__ . '/public/data/articles.json';
$articles = [];

if (file_exists($filename)) {
    // Le fichier JSON existe, essayez de le charger
    $articles_json = file_get_contents($filename);

    if ($articles_json === false) {
        // Erreur lors de la lecture du fichier JSON
        die("Erreur lors de la lecture du fichier JSON.");
    }

    // Essayez de décoder le JSON
    $articles = json_decode($articles_json, true);

    if ($articles === null) {
        // Erreur lors du décodage du JSON
        die("Erreur lors du décodage du JSON.");
    }
} else {
    // Le fichier JSON n'existe pas
    die("Le fichier JSON n'a pas été trouvé.");
}

// Vérifiez s'il y a des données de plats
if (empty($articles)) {
    die("Il n'y a pas de données de plats dans le fichier JSON.");
}

// Liste prédéfinie de préférences alimentaires
$listePreferences = ["Végétarien", "Végétalien", "Sans gluten", "Cétogène", "Autre"];

// Vérifiez si les champs existent déjà dans les informations de l'utilisateur
$preferences = isset($utilisateur['preferencias_alimentaires']) ? $utilisateur['preferencias_alimentaires'] : [];
$plats_favoris = isset($utilisateur['plats_favoris']) ? $utilisateur['plats_favoris'] : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_preference'])) {
        $nouvellePreference = $_POST['nouvelle_preference'];
        $preferences[] = $nouvellePreference;
    } elseif (isset($_POST['remove_preference'])) {
        $indiceASupprimer = $_POST['indice_preference'];
        if (isset($preferences[$indiceASupprimer])) {
            unset($preferences[$indiceASupprimer]);
            $preferences = array_values($preferences); // Réindexer le tableau
        }
    } elseif (isset($_POST['add_favorite'])) {
        $nouveauPlatFavori = $_POST['nouveau_plat_favori'];
        $plats_favoris[] = $nouveauPlatFavori;
    } elseif (isset($_POST['remove_favorite'])) {
        $indiceASupprimer = $_POST['indice_plat_favori'];
        if (isset($plats_favoris[$indiceASupprimer])) {
            unset($plats_favoris[$indiceASupprimer]);
            $plats_favoris = array_values($plats_favoris); // Réindexer le tableau
        }
    }

    // Mettre à jour les données de l'utilisateur dans le fichier JSON
    $utilisateur['preferencias_alimentaires'] = $preferences;
    $utilisateur['plats_favoris'] = $plats_favoris;

    // Vous pouvez également enregistrer ces informations dans le fichier JSON ici
    // Assurez-vous d'ajuster la structure du fichier JSON en conséquence.
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Processamento do formulário existente

    // Après le traitement du formulaire, rechargez les données JSON des articles
    $articles = json_decode(file_get_contents($filename), true);

    // Mettre à jour $plats_favoris avec les plats favoris actuels de l'utilisateur
    $plats_favoris = isset($utilisateur['plats_favoris']) ? $utilisateur['plats_favoris'] : [];
}



?>

<!DOCTYPE html>
<html>

<head>
    <?php require_once 'includes/head.php'; ?>
    <link rel="stylesheet" href="public/css/index.css">
    <title>FoodieShare - Mon Profil</title>
</head>

<body>
    <div class="container">
        <?php require_once 'includes/header_profil.php'; ?>
        <div class="content">
            <h1>Mon Profil</h1>
            <h2>Informations personnelles</h2>
            <p>Nom d'utilisateur : <?php echo $utilisateur['nom']; ?></p>
            <p>Email : <?php echo $utilisateur['email']; ?></p>

            <h2>Préférences alimentaires</h2>
            <form method='post'>
                <ul>
                    <?php foreach ($preferences as $indice => $preference) : ?>
                        <li>
                            <?php echo $preference; ?>
                            <input type="hidden" name="indice_preference" value="<?php echo $indice; ?>">
                            <button type="submit" name="remove_preference">Supprimer</button>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <select name="nouvelle_preference">
                    <?php foreach ($listePreferences as $preference) : ?>
                        <option value="<?php echo $preference; ?>"><?php echo $preference; ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" name="add_preference">Ajouter</button>
            </form>


            <h2>Mes plats préférés</h2>
            <form method='post'>
                <ul>
                    <?php
                    // Parcourir les articles pour afficher une checkbox à côté de chaque article
                    foreach ($articles as $indice => $article) {
                        $titre = $article['titre'];
                        $isChecked = in_array($titre, $plats_favoris); // Vérifier si l'article est déjà favori
                        echo '<li>';
                        echo '<input type="checkbox" name="plats_favoris[]" value="' . $titre . '" ' . ($isChecked ? 'checked' : '') . '>';
                        echo '<label>' . $titre . '</label>';
                        echo '</li>';
                    }
                    ?>
                </ul>
                <button type="submit" name="add_favorite">Ajouter les favoris</button>
            </form>


           
        </div>
        <?php require_once 'includes/footer.php'; ?>
    </div>
</body>

</html>