<?php include_once('includes/init.php'); ?>

<?php
if (!isset($_SESSION['USER_LOGGED'])) {
    include_once('includes/redirect_backward.php');
    exit();
}

// mode extrême désactivé pour le moment
if (isset($_POST['difficulte']) && !empty($_POST['difficulte'])) {
    $difficulte = $_POST['difficulte'];
    $nbLettre = 0;
    $accent = false;
    switch ($difficulte) {
        case 'facile':
            $nbLettre = rand(3, 5);
            break;
        case 'normale':
            $nbLettre = rand(4, 7);
            break;
        case 'extreme':
            $accent = true;
        case 'difficile':
            $nbLettre = -1;
            break;
    }

    $tabMot = array();
    if ($nbLettre != -1) {
        // Select mot dans la base de donnée avec nb lettre = $nbLettre
        try {
            $sqlQuery = 'SELECT * FROM mot WHERE longueur_mot = :nbLettre';
            $sqlStatement = $mysqlClient->prepare($sqlQuery);
            $sqlStatement->execute([
                'nbLettre' => $nbLettre
            ]);
            $tabMot = $sqlStatement->fetchAll();
            if (count($tabMot) == 0) {
                $_SESSION['ERROR_MSG'] = 'Impossible de trouver un mot de ' . $nbLettre . ' lettres</br>';
                include_once('includes/error.php');
                exit();
            }
        } catch (Exception $e) {
            $_SESSION['ERROR_MSG'] = 'Erreur lors de l\'éxécution de la requête SQL:</br>' . $e->getMessage();
            include_once('includes/error.php');
            exit();
        }
    } else {
        // select mot dans la base de donnée avec nb lettre > 7
        try {
            $sqlQuery = 'SELECT * FROM mot WHERE longueur_mot > 7';
            $sqlStatement = $mysqlClient->prepare($sqlQuery);
            $sqlStatement->execute();
            $tabMot = $sqlStatement->fetchAll();
            if (count($tabMot) == 0) {
                $_SESSION['ERROR_MSG'] = 'Impossible de trouver un mot de plus de 7 lettres</br>';
                include_once('includes/error.php');
                exit();
            }
        } catch (Exception $e) {
            $_SESSION['ERROR_MSG'] = 'Erreur lors de l\'éxécution de la requête SQL:</br>' . $e->getMessage();
            include_once('includes/error.php');
            exit();
        }
    }

    // choisie un mot au hasard dans le tableau
    $mot = $tabMot[rand(0, count($tabMot) - 1)];

    //création de la partie
    try {
        $sqlQuery = 'INSERT INTO partie (date_depart_partie, id_utilisateur_partie, id_mot_partie) VALUES (NOW(), :id_utilisateur_partie, :id_mot_partie)';
        $sqlStatement = $mysqlClient->prepare($sqlQuery);
        $sqlStatement->execute([
            'id_utilisateur_partie' => $_SESSION['USER_ID'],
            'id_mot_partie' => $mot['id_mot']
        ]);
    } catch (Exception $e) {
        $_SESSION['ERROR_MSG'] = 'Erreur lors de l\'éxécution de la requête SQL:</br>' . $e->getMessage();
        include_once('includes/error.php');
        exit();
    }

    // envoie sur la page partie.php avec l'id de la partie
    $partieNewId = $mysqlClient->lastInsertId();
    $_SESSION['REDIRECT_URL'] = 'partie.php?id=' . $partieNewId;
    include_once('includes/redirect.php');
    exit();
}
?>
<!DOCTYPE html>
<html>

<head>
    <?php include_once('includes/head.php'); ?>
    <title>Accueil</title>
</head>

<body>
    <header>
        <?php require("includes/header.php"); ?>
    </header>
    <div class="main_div">
        <div class="center_div">
            <form action="" method="post" id ="test">
                <!-- Choix difficulté facile/normale/difficile/extrême -->
                <div id="radio_difficulte">
                    <div class="choix_radio">
                        <input type="radio" name="difficulte" id="difficulte_facile" value="facile">
                        <label for="difficulte_facile">Facile</label>
                    </div>

                    <div class="choix_radio">
                        <input type="radio" name="difficulte" id="difficulte_normale" value="normale" checked>
                        <label for="difficulte_normale">Normale</label>
                    </div>

                    <div class="choix_radio">
                        <input type="radio" name="difficulte" id="difficulte_difficile" value="difficile">
                        <label for="difficulte_difficile">Difficile</label>
                    </div>

                    <!--
            <div class="choix_radio">
                <label for="difficulte_extreme">Extrême</label>
                <input type="radio" name="difficulte" id="difficulte_extreme" value="extreme">
            </div>
-->
                </div> <!-- Fin choix difficulté -->

                <div id="choice_button">
                    <input type="submit" value="Jouer">
                </div>
            </form>
        </div>
    </div>
    <footer><?php include_once('includes/footer.php'); ?></footer>
</body>

</html>