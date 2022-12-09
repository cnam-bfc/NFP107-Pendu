<?php include_once('includes/init.php'); ?>

<?php
// Méthode qui retourne le string sans accents
function sansAccents($string)
{
    $string = str_replace(
        array('à', 'â', 'ä', 'á', 'ã', 'å', 'ª', 'À', 'Â', 'Ä', 'Á', 'Ã', 'Å'),
        array('a', 'a', 'a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A', 'A', 'A'),
        $string
    );

    $string = str_replace(
        array('è', 'ê', 'ë', 'é', 'È', 'Ê', 'Ë', 'É'),
        array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
        $string
    );

    $string = str_replace(
        array('ì', 'î', 'ï', 'í', 'Ì', 'Î', 'Ï', 'Í'),
        array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
        $string
    );

    $string = str_replace(
        array('ò', 'ô', 'ö', 'ó', 'õ', 'ø', 'º', 'Ò', 'Ô', 'Ö', 'Ó', 'Õ', 'Ø'),
        array('o', 'o', 'o', 'o', 'o', 'o', 'o', 'O', 'O', 'O', 'O', 'O', 'O'),
        $string
    );

    $string = str_replace(
        array('ù', 'û', 'ü', 'ú', 'Ù', 'Û', 'Ü', 'Ú'),
        array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
        $string
    );

    $string = str_replace(
        array('ý', 'ÿ', 'Ý', 'Ÿ'),
        array('y', 'y', 'Y', 'Y'),
        $string
    );

    $string = str_replace(
        array('ñ', 'Ñ', 'ç', 'Ç'),
        array('n', 'N', 'c', 'C',),
        $string
    );

    return $string;
}

// Méthode qui retourne l'image du pendu en fonction du nombre d'erreur
function getPenduImage()
{
    $path = 'res/img/etapes_pendu/etape_' . count($_SESSION['PARTIE_LETTRE_FAUX']) . '.png';
    return $path;
}
?>

<?php
if (!isset($_SESSION['USER_LOGGED'])) {
    include_once('includes/redirect_backward.php');
    exit();
}

if (empty($_GET['id'])) {
    include_once('includes/redirect_backward.php');
    exit();
}

$result = "";

// Si nouvelle partie
if (empty($_SESSION['PARTIE_ID']) || $_SESSION['PARTIE_ID'] != $_GET['id']) {
    try {
        $sqlQuery = 'SELECT * FROM partie, mot WHERE id_utilisateur = :id_utilisateur AND partie.id_partie = :id_partie AND partie.id_mot = mot.id_mot';
        $sqlStatement = $mysqlClient->prepare($sqlQuery);
        $sqlStatement->execute([
            'id_utilisateur' => $_SESSION['USER_ID'],
            'id_partie' => $_GET['id']
        ]);
        $partie = $sqlStatement->fetch();
    } catch (Exception $e) {
        $_SESSION['ERROR_MSG'] = 'Erreur lors de l\'éxécution de la requête SQL:</br>' . $e->getMessage();
        include_once('includes/error.php');
        exit();
    }

    // Partie déjà terminé
    if (empty($partie) || $partie['date_fin_partie'] !== null) {
        $_SESSION['REDIRECT_URL'] = './';
        include_once('includes/redirect.php');
        exit();
    }

    $_SESSION['PARTIE_ID'] = $partie['id_partie'];
    $_SESSION['PARTIE_MOT'] = sansAccents(strtoupper($partie['nom_mot']));
    $_SESSION['PARTIE_SCORE'] = 0;
    $_SESSION['PARTIE_LETTRE_TROUVE'] = array();
    $_SESSION['PARTIE_LETTRE_FAUX'] = array();
}

// Si une lettre est envoyé
if (!empty($_POST['lettre']) && count($_SESSION['PARTIE_LETTRE_FAUX']) < 11) {
    $lettre = strtoupper(trim($_POST['lettre']));
    if (strlen($lettre) == 1) {
        $mot = $_SESSION['PARTIE_MOT'];

        // Si la lettre est déjà trouvé
        if (in_array($lettre, $_SESSION['PARTIE_LETTRE_TROUVE'])) {
            $result .= 'La lettre ' . $lettre . ' a déjà été trouvée';
        } elseif (in_array($lettre, $_SESSION['PARTIE_LETTRE_FAUX'])) {
            $result .= 'La lettre ' . $lettre . ' a déjà été essayée';
        } else {
            // Si la lettre est dans le mot
            if (strpos($mot, $lettre) !== false) {
                $_SESSION['PARTIE_LETTRE_TROUVE'][] = $lettre;
                $result .= 'La lettre ' . $lettre . ' est dans le mot';
            } else {
                $_SESSION['PARTIE_LETTRE_FAUX'][] = $lettre;
                $result .= 'La lettre ' . $lettre . ' n\'est pas dans le mot';
            }
        }
    } else {
        $result .= 'La lettre doit faire 1 caractère';
    }
    $result .= '</br>';
}

// Si le mot est trouvé
$lettresMot = str_split($_SESSION['PARTIE_MOT']);
$lettresNonTrouve = array_diff(array_unique($lettresMot), $_SESSION['PARTIE_LETTRE_TROUVE']);

// Si toutes les lettres sont trouvées
if (empty($lettresNonTrouve)) {
    $result .= 'Vous avez trouvé le mot !';
    $_SESSION['PARTIE_SCORE'] += 10 - count($_SESSION['PARTIE_LETTRE_FAUX']);
    try {
        $sqlQuery = 'UPDATE partie SET score_partie = :score_partie, date_fin_partie = NOW() WHERE id_partie = :id_partie';
        $sqlStatement = $mysqlClient->prepare($sqlQuery);
        $sqlStatement->execute([
            'score_partie' => $_SESSION['PARTIE_SCORE'],
            'id_partie' => $_SESSION['PARTIE_ID']
        ]);
    } catch (Exception $e) {
        $_SESSION['ERROR_MSG'] = 'Erreur lors de l\'éxécution de la requête SQL:</br>' . $e->getMessage();
        include_once('includes/error.php');
        exit();
    }
}

// Si le nombre de fautes est atteint
if (count($_SESSION['PARTIE_LETTRE_FAUX']) >= 11) {
    $result .= 'Vous avez perdu !';
    try {
        $sqlQuery = 'UPDATE partie SET score_partie = :score_partie, date_fin_partie = NOW() WHERE id_partie = :id_partie';
        $sqlStatement = $mysqlClient->prepare($sqlQuery);
        $sqlStatement->execute([
            'score_partie' => $_SESSION['PARTIE_SCORE'],
            'id_partie' => $_SESSION['PARTIE_ID']
        ]);
    } catch (Exception $e) {
        $_SESSION['ERROR_MSG'] = 'Erreur lors de l\'éxécution de la requête SQL:</br>' . $e->getMessage();
        include_once('includes/error.php');
        exit();
    }
}

$strMot = '';
foreach ($lettresMot as $lettre) {
    if (in_array($lettre, $_SESSION['PARTIE_LETTRE_TROUVE'])) {
        $strMot .= $lettre;
    } else {
        $strMot .= '_';
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <?php include_once('includes/head.php'); ?>
    <title>Partie</title>
</head>

<body>
    <header>
        <?php require("includes/header.php"); ?>
    </header>
    <div class="main_div">
        <div class="center_div">
            <h1>Partie</h1>
            <img src="<?php echo getPenduImage() ?>" alt="Pendu" id="pendu_img">
            <p>Mot : <?php echo $strMot; ?></p>
            <p><?php echo $result; ?></p>
            <p>Score : <?php echo $_SESSION['PARTIE_SCORE']; ?></p>
            <p>Lettres trouvées : <?php echo implode(', ', $_SESSION['PARTIE_LETTRE_TROUVE']); ?></p>
            <p>Lettres fausses : <?php echo implode(', ', $_SESSION['PARTIE_LETTRE_FAUX']); ?></p>
            <form action="" method="POST">
                <input type="text" name="lettre" minlength="1" maxlength="1" placeholder="Lettre" autocomplete="off">
                <input type="submit" value="Envoyer">
            </form>
        </div>
    </div>
    <footer><?php include_once('includes/footer.php'); ?></footer>
</body>

</html>