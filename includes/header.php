<header>
    <a href='index.php' class="logo">FoodieShare</a>
    <ul class="header-menu">
        <li class=<?php $_SERVER['REQUEST_URI'] === '/add-article.php' ? 'active' : ''?>>
            <a href='add-article.php'><b>Ajouter un avis</b></a>
        </li>

        <li>Pas encore de compte ? <a href="connexion.php"><b>Inscrivez-vous</b></a></li>

        <li><a href="connexion.php"><b>Connectez-vous</b></a></li>

        <!-- Ajout du champ de recherche -->
        <li>
            <form action="/" method="get">
                <input type="text" name="search" placeholder="Rechercher">
                <button type="submit">Rechercher</button>
            </form>
        </li>
    </ul>
</header>
