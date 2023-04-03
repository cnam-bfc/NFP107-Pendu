<?php include_once('includes/init.php'); ?>

<?php
if (!isset($_SESSION['USER_LOGGED']) || ($_SESSION['USER_LOGIN'] != 'totor' && $_SESSION['USER_LOGIN'] != 'Banban')) {
    include_once('includes/redirect_backward.php');
    exit();
}

if (isset($_FILES['fileToUpload']) && !empty($_FILES['fileToUpload'])) {
    if ($_FILES['fileToUpload']['error'] > 0) {
        $_SESSION['ERROR_MSG'] = 'Erreur lors du transfert du fichier.';
        include_once('includes/error.php');
        exit();
    }

    $file = $_FILES['fileToUpload'];
    $file = fopen($file["tmp_name"], "r"); // Ouverture du fichier en lecture seule

    // Tableau des mots qui n'ont pas pu être insérés en bdd
    $tabErreur = [];

    // Tableau des lettres avec accents minuscules et majuscules
    $tabLettreAccents = ['à', 'â', 'ä', 'ç', 'é', 'è', 'ê', 'ë', 'î', 'ï', 'ô', 'ö', 'ù', 'û', 'ü', 'À', 'Â', 'Ä', 'Ç', 'É', 'È', 'Ê', 'Ë', 'Î', 'Ï', 'Ô', 'Ö', 'Ù', 'Û', 'Ü'];
    // Tableau des voyelles minuscules et majuscules avec accents
    $tabVoyelle = ['a', 'e', 'i', 'o', 'u', 'y', 'A', 'E', 'I', 'O', 'U', 'Y', 'à', 'â', 'ä', 'é', 'è', 'ê', 'ë', 'î', 'ï', 'ô', 'ö', 'ù', 'û', 'ü', 'À', 'Â', 'Ä', 'É', 'È', 'Ê', 'Ë', 'Î', 'Ï', 'Ô', 'Ö', 'Ù', 'Û', 'Ü'];

    $result = "";

    while (!feof($file)) { // Tant qu'on est pas à la fin du fichier
        $word = fgets($file); // On lit la ligne courante qui représente un mot
        // On nettoie le mot
        $word = trim($word); // On supprime les espaces en début et fin de chaîne
        // On détecte l'encodage du mot
        $word = mb_convert_encoding($word, 'UTF-8', mb_detect_encoding($word, ['UTF-8', 'ISO-8859-15', 'ISO-8859-1', 'ASCII'], true));
        // On remplace les caractères spéciaux du type 'œ'
        $word = str_replace('œ', 'oe', $word);
        $word = str_replace('Œ', 'Oe', $word);
        $word = str_replace('æ', 'ae', $word);
        $word = str_replace('Æ', 'Ae', $word);
        if ($word == '') continue; // Si le mot est vide, on passe au mot suivant (continue

        // vérification si le mot pas déjà présent en bdd
        try {
            $sqlQuery = 'SELECT nom_mot FROM mot WHERE nom_mot = :nom_mot';
            $sqlStatement = $pdo->prepare($sqlQuery);
            $sqlStatement->execute([
                'nom_mot' => $word
            ]);
            $verifMot = $sqlStatement->fetchAll();
            if (count($verifMot) > 0) {
                $tabErreur[] = $word;
                continue;
            }
        } catch (Exception $e) {
            $_SESSION['ERROR_MSG'] = 'Erreur lors de l\'éxécution de la requête SQL:</br>' . $e->getMessage();
            include_once('includes/error.php');
            exit();
        }

        // Traitement du mot
        $nbLettres = strlen($word);
        $nbVoyelles = 0;
        $nbCaracteresSpeciaux = 0;

        // Parcourir le mot lettre par lettre et compter les voyelles et les caractères spéciaux
        for ($i = 0; $i < $nbLettres; $i++) {
            // Si la lettre est une voyelle
            if (in_array($word[$i], $tabVoyelle)) {
                $nbVoyelles++;
            }
            // Si la lettre est un caractère spécial
            if (in_array($word[$i], $tabLettreAccents)) {
                $nbCaracteresSpeciaux++;
            }
        }

        // Insertion du mot en bdd
        try {
            $sqlQuery = 'INSERT INTO mot (nom_mot, longueur_mot, nombre_voyelle, nombre_caracteres_speciaux) VALUES (:nom_mot, :nb_lettres, :nb_voyelles, :nb_caracteres_speciaux)';
            $sqlStatement = $pdo->prepare($sqlQuery);
            $sqlStatement->execute([
                'nom_mot' => $word,
                'nb_lettres' => $nbLettres,
                'nb_voyelles' => $nbVoyelles,
                'nb_caracteres_speciaux' => $nbCaracteresSpeciaux
            ]);
        } catch (Exception $e) {
            $_SESSION['ERROR_MSG'] = 'Erreur lors de l\'éxécution de la requête SQL:</br>' . $e->getMessage();
            include_once('includes/error.php');
            exit();
        }
    }
    fclose($file);
    $result .= 'Importation réussie !';
    if (count($tabErreur) > 0) {
        $result .= '</br>Les mots suivants n\'ont pas pu être insérés en bdd car ils y étaient déjà :</br>';
        foreach ($tabErreur as $mot) {
            $result .= $mot . '</br>';
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <?php include_once('includes/head.php'); ?>
    <title>Import mots</title>
</head>

<body>
    <header><?php include_once('includes/header.php'); ?></header>
    <div class="main_div">
        <div class="center_div">
            <form action="" method="post" enctype="multipart/form-data">
                <h2>Importer des mots</h2>
                <label for="fileToUpload">Fichier à importer :</label>
                <input type="file" name="fileToUpload" id="fileToUpload">

                <input type="submit" value="Importer les mots" name="submit">
            </form>
            <?php if (isset($result)) {
                echo '<p>' . $result . '</p>';
            } ?>
        </div>
    </div>
    <header><?php include_once('includes/footer.php'); ?></header>
</body>

</html>