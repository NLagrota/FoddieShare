<!DOCTYPE html>
<html>

<head>
    <?php require_once 'includes/head.php'; ?>
    <link rel="stylesheet" href="public/css/index.css">
    <title>FoodieShare - Mon Profil</title>
</head>

<body>
    <div class="container">
        <?php require_once 'includes/header_inscription.php'; ?>
        <div class="content">
            <?php
            session_start();
            if (!isset($_SESSION['user'])) {
                header("Location: inscription.php");
                exit();
            }

            $usuario = $_SESSION['user'];

            // Verifique se os campos já existem nos dados do usuário
            $preferencias = isset($usuario['preferencias_alimentaires']) ? $usuario['preferencias_alimentaires'] : [];
            $plats_favoris = isset($usuario['plats_favoris']) ? $usuario['plats_favoris'] : [];

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (isset($_POST['add_preference'])) {
                    $novaPreferencia = $_POST['nova_preferencia'];
                    $preferencias[] = $novaPreferencia;
                } elseif (isset($_POST['remove_preference'])) {
                    $indiceRemover = $_POST['indice_preferencia'];
                    if (isset($preferencias[$indiceRemover])) {
                        unset($preferencias[$indiceRemover]);
                        $preferencias = array_values($preferencias); // Reindexar o array
                    }
                } elseif (isset($_POST['add_favorite'])) {
                    $novoPlatFavori = $_POST['novo_plat_favori'];
                    $plats_favoris[] = $novoPlatFavori;
                } elseif (isset($_POST['remove_favorite'])) {
                    $indiceRemover = $_POST['indice_plat_favori'];
                    if (isset($plats_favoris[$indiceRemover])) {
                        unset($plats_favoris[$indiceRemover]);
                        $plats_favoris = array_values($plats_favoris); // Reindexar o array
                    }
                }

                // Atualizar os dados do usuário no arquivo JSON
                $usuario['preferencias_alimentaires'] = $preferencias;
                $usuario['plats_favoris'] = $plats_favoris;

                // Você também pode salvar essas informações no arquivo JSON aqui
                // Certifique-se de ajustar a estrutura do arquivo JSON de acordo com esses campos.
            }
            ?>

            <h1>Mon Profil</h1>
            <h2>Informations personnelles</h2>
            <p>Nom d'utilisateur : <?php echo $usuario['nom']; ?></p>
            <p>Email : <?php echo $usuario['email']; ?></p>

            <h2>Préférences alimentaires</h2>
            <form method='post'>
                <ul>
                    <?php foreach ($preferencias as $indice => $preferencia) : ?>
                        <li>
                            <?php echo $preferencia; ?>
                            <input type="hidden" name="indice_preferencia" value="<?php echo $indice; ?>">
                            <button type="submit" name="remove_preference">Supprimer</button>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <input type="text" name="nova_preferencia" placeholder="Nouvelle préférence">
                <button type="submit" name="add_preference">Ajouter</button>
            </form>

            <h2>Mes plats préférés</h2>
            <form method='post'>
                <ul>
                    <?php foreach ($plats_favoris as $indice => $plat_favori) : ?>
                        <li>
                            <?php echo $plat_favori; ?>
                            <input type="hidden" name="indice_plat_favori" value="<?php echo $indice; ?>">
                            <button type="submit" name="remove_favorite">Supprimer</button>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <input type="text" name="novo_plat_favori" placeholder="Nouveau plat préféré">
                <button type="submit" name="add_favorite">Ajouter</button>
            </form>
        </div>
        <?php require_once 'includes/footer.php'; ?>
    </div>
</body>

</html>
