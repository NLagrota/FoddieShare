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

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <?php require_once 'includes/head.php' ?>
  <link rel="stylesheet" href="public/css/show-article.css">
  <title>Article</title>
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
      </div>
    </div>

    <!-- Ajouter bouton pour modifier article --> 
      <div class="action">
        <a class="btn btn-primary" href="/add-article.php?id=<?=$id?>">Editer l'article</a> 
        <a class="btn btn-primary" href="/delete-article.php?id=<?=$id?>">Supprimer l'article</a> 
      </div>


    <?php require_once 'includes/footer.php'?>

  </div>
</body>

</html>