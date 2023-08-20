<?php 
$filename = __DIR__ . "/public/data/articles.json";

$articles = [];
$articles = json_decode(file_get_contents($filename), true) ?? []; //recuperer le tableau en json


$_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$id = $_GET['id'] ?? '';



if ($id) {
  $articleIndex = array_search($id, array_column($articles, 'id'));
  //print_r(array_column($articles, 'id'));//test


  $article = $articles[$articleIndex];
  $titre = $article['titre'];
  $image = $article['image'];
  $etoiles = $article['$etoiles'];
  $prix = $article['$prix'];
  $localisation = $article['$localisation'];
  $categorie = $article['categorie'];
  $contenu = $article['contenu'];
}

// si le id n'est pas definit alors rediriger vers index.php
if (!$id) {
  header('Location: /index.php'); //mudei, antes era só /
  // sinon: cad si id est definit, alors initialiser l'article en 
  // Le chargeant en utilisant son id a partir du fichier articles.json (la base de donnee
} else {
  if (file_exists($filename)) {
    $articles = json_decode(file_get_contents($filename), true) ?? [];
    $articleIndex = array_search($id, array_column($articles, 'id'));
    $article = $articles[$articleIndex];
    // NOTE: toujours afficher le resultat apres chaque instruction pour
    // mieux comprendre, corriger Les fautes !!
    //print_r($article)
  }
}

// Vérifier si l'utilisateur est connecté (c'est une étape simplifiée) -- dernier mod
$isUserLoggedIn = true; // Par exemple, vérifiez si l'utilisateur est connecté.

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isUserLoggedIn) {
    // L'utilisateur a soumis un avis
    $newReview = [
        'utilisateur' => 'Nom de l\'utilisateur', // Remplacez par le nom de l'utilisateur connecté.
        'note' => $_POST['note'],
        'commentaire' => $_POST['commentaire'],
    ];

    // Ajoutez l'avis à l'article
    $article['avis'][] = $newReview;

    // Mettez à jour le fichier JSON avec le nouvel avis
    file_put_contents($filename, json_encode($articles, JSON_PRETTY_PRINT));

    // Redirigez pour éviter la soumission en double
    header('Location: show-article.php?id=' . $id);
}

$note = 4; // Note donnée (peut être un nombre de 1 à 5)

// Vérifiez si la note est dans la plage autorisée (1 à 5)
if ($note < 1) {
    $note = 1;
} elseif ($note > 5) {
    $note = 5;
}

// Fonction pour afficher les étoiles en fonction de la note
function afficherEtoiles($note) {
    $etoiles = '';
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $note) {
            $etoiles .= '★'; // Étoile remplie
        } else {
            $etoiles .= '☆'; // Étoile vide
        }
    }
    return $etoiles;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <?php require_once 'includes/head.php' ?>
  <link rel="stylesheet" href="public/css/show-article.css">
  <title>Article</title>

  <style>
/* Style pour les étoiles de notation */
.rating-stars {
    font-size: 24px; /* Taille des étoiles */
    color: #FFD700; /* Couleur des étoiles remplies */
}

.rating-stars::before {
    content: "\2605"; /* Code unicode pour une étoile remplie */
    margin-right: 4px; /* Espacement entre les étoiles */
}

.rating-stars::after {
    content: "\2606"; /* Code unicode pour une étoile vide */
}
    </style>



</head>

<body>
<div class="container">
        <?php require_once 'includes/header.php' ?>
        <div class="content">
            <div class="article-container">
                <a class="article-back" href="./index.php">Retour à la liste des articles</a>
                <div class="article-cover-img" style="background-image:url(<?=$article['image']?>)"></div>
                <h1 class="article-title"><?=$article['titre']?></h1>
                <div class="separator"></div>
                <p class="article-content"><?=$article['contenu']?></p>

                <!-- Afficher des étoiles de notation -->
                <div class="rating-stars">
                    <?php echo afficherEtoiles($note); ?>
                </div>
            </div>
        </div>

    <!-- Formulaire pour soumettre un avis -->

    <?php if ($isUserLoggedIn) : ?>

        <h2>Laissez un avis</h2>
        <form method="post">
            <label for="note">Note :</label>
            <input type="number" name="note" min="1" max="5" required>

            <label for="commentaire">Commentaire :</label>
            <textarea name="commentaire" required></textarea>

            <button type="submit">Soumettre l'avis</button>
        </form>
        <?php else : ?>
        <p>Connectez-vous pour laisser un avis.</p>
    <?php endif; ?>

    <!-- Affichage des avis existants -->
    <?php if (isset($article['avis'])) : ?>
        <h2>Avis des utilisateurs</h2>
        <ul>
            <?php foreach ($article['avis'] as $review) : ?>
                <li>
                    <strong><?=$review['utilisateur']?></strong> 
                    (Note : <?=$review['note']?>)
                    <p><?=$review['commentaire']?></p>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <!-- Ajouter bouton pour modifier article --> 
      <div class="action">
        <a class="btn btn-primary" href="/add-article.php?id=<?=$id?>">Editer l'article</a> 
        <a class="btn btn-primary" href="/delete-article.php?id=<?=$id?>">Supprimer l'article</a> 
      </div>


    <?php require_once 'includes/footer.php'?>

  </div>
</body>

</html>