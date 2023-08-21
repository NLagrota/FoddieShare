<?php
$filename = __DIR__ . '/public/data/articles.json';

$articles = [];
$id = $_GET['id'] ?? '';
print_r($filename);
$errors = [
    'titre' => '',
    'image' => '',
    'etoiles' => '',
    'categorie' => '',
    'prix' => '',
    'localisation' => '',
    'contenu' => '',
];

//initialiser les variables filtrees et validees
$titre = $_POST['titre'] ?? '';
print_r($titre);
$image = $_POST['image'] ?? '';
$etoiles = $_POST['etoiles'] ?? '';
$categorie = $_POST['categorie'] ?? '';
$prix = $_POST['prix'] ?? '';
$localisation = $_POST['localisation'] ?? ''; // Adicione o campo "la localisation"
$contenu = $_POST['contenu'] ?? '';


if (!$titre) {
    $errors['titre'] = 'Saisir le titre svp !';
}

if (!$image) {
    $errors['image'] = "Entrer l'URL de l'image svp !";
} elseif (!filter_var($image, FILTER_VALIDATE_URL)) {
    $errors['image'] = "Entrer une URL valide de l'image svp ! ";
}
if (!$etoiles) {
    $errors['etoiles'] = 'Saisir la note svp !';
}
if (!$categorie) {
    $errors['categorie'] = 'Saisir la catégorie svp !';
}

if (!$prix) {
    $errors['prix'] = 'Saisir le prix svp !';
}
if (!$localisation) {
    $errors['localisation'] = 'Saisir la localisation svp !';
}

if (!$contenu) {
    $errors['contenu'] = 'Saisir les ingrédients svp !';
}
// Essayer toujours !! d'afficher vos variables pour comprendre
// mieux le fonctionnement
//print_r($filename);

if (file_exists($filename)) {
    // Si le contenu du fichier est pas vide alors, obtenir le contenu du fichier puis,
    // convertir le format json en un tableau PHP associatif
    // ?? [] : sinon affecter a la variable $ articles un tableau vide []
    // Ceci evite des erreurs dans le code plus tard en initialisant de
    // toute facon la variable $articles
    $articles = json_decode(file_get_contents($filename), true) ?? [];
    print_r($articles);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Filtrer les donnees du formulair
    $_POST = filter_input_array(
        INPUT_POST,
        [
            'titre' => FILTER_SANITIZE_SPECIAL_CHARS,
            'image' => FILTER_SANITIZE_URL,
            'etoiles' => FILTER_SANITIZE_NUMBER_INT,
            'categorie' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'prix' => FILTER_SANITIZE_NUMBER_INT, 
            'localisation' => FILTER_SANITIZE_SPECIAL_CHARS, 
            'contenu' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        ]
    );



    // empty retourne true si uen variable est vide
    // array_filter($errors, fn($e) => $e !== '') retourne un nouveau tableau associatif
    //qui contient uniquement les elements dont la valeur nest pas une chaine de
    // characteres vide. Cela evite de soumettre le formulaire avec au moins une erreur
    // (possibilites de attaque)

    if (empty(array_filter($errors, fn ($e) => $e !== ''))) {
        // si le $id est definit dans $_GET alors mettre a jour l'article
        if ($id) {
            $articles[$articleIndex]['titre'] = $titre;
            $articles[$articleIndex]['image'] = $image;
            $articles[$articleIndex]['etoiles'] = $etoiles;
            $articles[$articleIndex]['categorie'] = $categorie;
            $articles[$articleIndex]['prix'] = $prix;
            $articles[$articleIndex]['localisation'] = $localisation;
            $articles[$articleIndex]['contenu'] = $contenu;
        }
        // Sinon, ajouter un nouvel article
        else {
            $articles = [
                ...$articles, [
                    'titre' => $titre,
                    'image' => $image,
                    'etoiles' => $etoiles,
                    'categorie' => $categorie,
                    'prix' => $prix,
                    'localisation' => $localisation,
                    'contenu' => $contenu,
                    'id' => time(),
                ],
            ];
        }

        file_put_contents($filename, json_encode($articles));
        header('Location: /');
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once 'includes/head.php' ?>
    <link rel="stylesheet" href="public/css/add-article.css">
    <title>Ajouter un avis</title>
</head>

<body>
    <div class="container">
        <?php require_once 'includes/header.php' ?>
        <div class="content">

            <div class="block p-20 form-container">
                <!-- <h1>Ajouter un article</h1> -->
                <h1><?= $id ? 'Modifier' : 'Ajouter' ?> un avis sur un repas</h1>
                <form action="add-article.php" method="post">

                    <!-- Titre -->
                    <div class="form-control">
                        <label for="titre">Repas</label>

                        <input type="text" name="titre" id="titre" value="<?= $titre ?? '' ?>">
                        <?php if ($errors['titre']) : ?>
                            <p class='text-danger'><?= $errors['titre'] ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Image -->
                    <div class="form-control">
                        <label for="image">Image</label>
                        <input type="text" name="image" id="image" value="<?= $image ?? '' ?>">
                        <?php if ($errors['image']) : ?>
                            <p class='text-danger'><?= $errors['image'] ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="form-control">
                        <label for="etoiles">Évaluation</label>
                        <select name="etoiles" id="etoiles">
                            <option value="1">1 étoile</option>
                            <option value="2">2 étoiles</option>
                            <option value="3">3 étoiles</option>
                            <option value="4">4 étoiles</option>
                            <option value="5">5 étoiles</option>
                        </select>
                        <?php if ($errors['etoiles']) : ?>
                            <p class='text-danger'><?= $errors['etoiles'] ?></p>
                        <?php endif; ?>

                    </div>

                    <!-- categorie-->
                    <div class="form-control">
                        <label for="categorie">Catégorie</label>
                        <select name="categorie" id="categorie">
                            <option value="">Non choisie</option>
                            <option <?= !$categorie || $categorie === "Boeuf" ? 'selected' : '' ?> value="Boeuf">Boeuf
                            </option>
                            <option <?= !$categorie || $categorie === "Crevettes" ? 'selected' : '' ?> value="Crevettes">
                                Crevettes</option>
                            <option <?= !$categorie || $categorie === "Pates" ? 'selected' : '' ?> value="Pates">
                                Pates</option>
                            <option <?= !$categorie || $categorie === "Dinde" ? 'selected' : '' ?> value="Dinde">
                                Dinde</option>
                            <option <?= !$categorie || $categorie === "Tofu" ? 'selected' : '' ?> value="Tofu">
                                Tofu</option>
                            <option <?= !$categorie || $categorie === "Saumon" ? 'selected' : '' ?> value="Saumon">
                                Saumon</option>
                        </select>

                        <?php if ($errors['categorie']) : ?>
                            <p class='text-danger'><?= $errors['categorie'] ?></p>
                        <?php endif; ?>

                    </div>


                    <div class="form-control">
                        <label for="prix">Prix</label>
                        <input type="text" name="prix" id="prix" value="<?= $prix ?? '' ?>">
                        <?php if ($errors['prix']) : ?>
                            <p class='text-danger'><?= $errors['prix'] ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- La Localisation -->
                    <div class="form-control">
                        <label for="localisation">Localisation</label>
                        <input type="text" name="localisation" id="localisation" value="<?= $localisation ?? '' ?>">
                        <?php if ($errors['localisation']) : ?>
                            <p class='text-danger'><?= $errors['localisation'] ?></p>
                        <?php endif; ?>
                    </div>





                    <!-- Contenu -->
                    <div class="form-control">
                        <label for="contenu">Ingrédients</label>
                        <textarea name="contenu" id="contenu"><?= $contenu ?? '' ?></textarea>

                        <?php if ($errors['contenu']) : ?>
                            <p class='text-danger'><?= $errors['contenu'] ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Boutons -->
                    <div class="form-actions">
                        <button class="btn btn-secondary" type="button">Annuler</button>
                        <!-- Changer Le bouton pour que ca soit dynamique :
                            Alors si id est définit, on va modifier l'article sinon on va le sauvegarder-->
                        <button class="btn btn-primary" type="submit"><?= $id ? 'Modifier' : 'Sauvegarder' ?></button>
                    </div>

                </form>

            </div>

        </div>

    </div>
    <?php require_once 'includes/footer.php' ?>

</body>

</html>