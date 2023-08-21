<?php
$filename = __DIR__. '/public/data/articles.json';
$articles = []; 
$categorie = [];
$GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$selectedCat = $_GET['cat'] ?? '';
$keyword = $_GET['search'] ?? ''; // Récupérer le mot-clé de recherche

function count_categories ($accumulateur, $valeur_courante)
{
    if (isset($accumulateur[$valeur_courante])) {
        $accumulateur[$valeur_courante]++;
    } else {
        $accumulateur[$valeur_courante] = 1;
    }
    return $accumulateur;
}
    
if (file_exists($filename)) {
    $articles = json_decode(file_get_contents($filename), true) ?? []; 
    // Reduire les cles du tableau en extrayant uniquement la categorie 
    $cattmp = array_map(fn($a) => $a['categorie'], $articles); 
    //print_r($cattmp);=> Array ( [0] => chalets [1] => chalets [2] => hotels [3] => hotels 
    // [4] => camping [5] => camping)
    
    //array_reduce est utilisé avec la fonction de rappel count_categories 
    // pour itérer sur le tableau $cattmp et accumuler les résultats dans le tableau $categories. 
    $categories = array_reduce($cattmp, 'count_categories', []); 
    //print_r($categories); => Array ( [chalets] => 2 [hotels] => 2 [camping] => 2 )
    //Pour chaque catégorie, énumérer tous les articles qui y sont associé :
    
    $articlesParCategorie = array_reduce($articles, 'classifier_articles', []);
    // file_put_contents($test_filename, json_encode($articlesParCategorie));
    print_r(json_encode($articlesParCategorie));
}

// Filtrer les articles en fonction du mot-clé de recherche****************************
if (!empty($keyword)) {
    $filteredArticles = array_filter($articles, function ($article) use ($keyword) {
        return stripos($article['titre'], $keyword) !== false || 
               stripos($article['description'], $keyword) !== false ||
               stripos($article['categorie'], $keyword) !== false;
    });
    // Utiliser $filteredArticles pour afficher les résultats
}

function classifier_articles($acc, $article)
{
    if (isset($acc[$article['categorie']])) {
        //crée un nouveau tableau en copiant tous les éléments existants 
        //du tableau $acc[$article['categorie']] à l'aide de l'opérateur
        //de décomposition (...) puis en ajoutant l'élément $article à la fin du tableau.
        $acc[$article['categorie']] = [...$acc[$article['categorie']], $article];
    } else {
        $acc[$article['categorie']] = [$article];
    } 
    return $acc;
}




?>


</html>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once 'includes/head.php' ?>
    <link rel="stylesheet" href="public/css/index.css">
    <title>FoodieShare</title>
</head>

<body>
    <div class="container">
        <?php require_once 'includes/header.php' ?>
    <div class="content">

     <div class="newsfeed-container"> 
            <div class="categorie-container">
                <ul>
                    <!-- Tous les articles -->
                    <li class=<?=$selectedCat ? '' : 'cat-active'?>>
                        <a href="/"> Tous les repas <span class="small">(<?=count($articles)?>)</span></a>
                    </li>
                    <!-- Libelles de toutes les categories -->
                    <?php foreach ($categories as $catName => $catNum): ?>
                    <li class=<?=$selectedCat === $catName ? "cat-active" : ''?>>
            
                        <a href="/?cat=<?=$catName?>"><?=$catName?> <span class="small">(
                                <?=$catNum?>)
                            </span></a>
                    </li>
                    <?php endforeach;?>
                </ul>
            </div>
        </div>

        <div class="newsfeed-content">
    <!-- Rajouter un test: (1) en cas ou la categorie est non selectionnee -->
    <?php if (!$selectedCat): ?>
        <!-- fin test -->
        <?php foreach ($categories as $cat => $num): ?>
        <h2>
            <?=$cat?>
        </h2>
        <div class="articles-container">
            <?php foreach ($articlesParCategorie[$cat] as $a): ?> 
            <!--Ajouter un lien pour afficher l'article-->
            <a href="show-article.php?id=<?=$a['id']?>" class="article block">    
            <!-- <div class="article block">  -->
                <!--Image-->   
                <div class="overflow"> 
                    <div class="img-container" style="background-image:url(<?=$a['image']?>)">
                    </div>
                </div>
                <!-- Titre avec mise en évidence du mot clé *********************************-->
                <h3>
                            <?=highlightKeyword($a['titre'], $keyword)?>
                        </h3>
                <!--Titre-->
                <h3>
                    <?=$a['titre']?>
                </h3>
            <!-- </div> -->
            </a>
            <?php endforeach;?>
        </div>
        <?php endforeach;?>
        <!--Ajoute un test : (2) en cas ou la categorie est selectionnee-->
        <?php else: ?>
        <h2><?=$selectedCat?></h2>
        <div class="articles-container">
            <?php foreach ($articlesParCategorie[$selectedCat] as $a): ?>
                <!--Ajouter un lien pour afficher l'article-->
                <a href="show-article.php?id=<?=$a['id']?>" class="article block">    
                <!-- <div class="article block"> -->
                <!--Image-->
                <div class="overflow">
                    <div class="img-container" style="background-image:url(<?=$a['image']?>)">
                    </div>
                </div>

                <!-- Titre avec mise en évidence du mot clé***************************** -->
                <h3>
                        <?=highlightKeyword($a['titre'], $keyword)?>
                    </h3>
                 <!--Titre-->
                <h3>
                    <?=$a['titre']?>
                </h3>
                <!-- </div> -->
            </a>
        <?php endforeach; ?>
        </div>
    <?php endif; ?> <!-- Fin du test -->

    <!-- Insérer le code ici pour afficher les résultats de la recherche****************************** -->
    <?php if (!empty($filteredArticles)): ?>
    <h2>Résultats de la recherche pour "<?php echo $keyword; ?>"</h2>
    <div class="articles-container">
        <?php foreach ($filteredArticles as $a): ?>
            <a href="show-article.php?id=<?=$a['id']?>" class="article block">
            <!--Image--->
                    <div class="overflow">
                        <div class="img-container" style="background-image:url(<?=$a['image']?>)"></div>
                    </div>
                    <!-- Titre avec mise en évidence du mot clé -->
                    <h3>
                        <?=highlightKeyword($a['titre'], $keyword)?>
                    </h3>
                    </a>
                        <?php endforeach; ?>
                    </div>

                    
                <?php else: ?>
                    <p>Aucun résultat trouvé pour "<?php echo $keyword; ?>"</p>
                <?php endif; ?>

</div>

    </div>
        <?php require_once 'includes/footer.php' ?>
    </div>

</body>

</html>

<?php
function highlightKeyword($text, $keyword) {
    return str_ireplace($keyword, '<strong>'.$keyword.'</strong>', $text);
}
?>