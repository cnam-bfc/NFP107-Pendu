<?php include_once('includes/init.php'); ?>

<?php

if (isset($_POST['fileToUpload']) && !empty($_POST['fileToUpload'])) {
    $file = $_POST['fileToUpload'];
    $file = fopen($file, "r"); // Ouverture du fichier en lecture seule

    $tabVoyelle = ['a', 'e', 'i', 'o', 'u', 'y']; // Tableau des voyelles
    $voyelles = "aeiouy";
    $tabLettreAccents = ['à', 'â', 'ä', 'é', 'è', 'ê', 'ë', 'î', 'ï', 'ô', 'ö', 'ù', 'û', 'ü', 'ç']; // Tableau des lettres avec accents

    $i = 0;
    while (!feof($file)) { // Tant qu'on est pas à la fin du fichier
        $word = fgets($file); // On lit la ligne courante qui représente un mot

        // vérification si le mot pas déjà présent en bdd
        try {
            $sqlQuery = 'SELECT * FROM mot WHERE nom_mot = :nom_mot';
            $sqlStatement = $mysqlClient->prepare($sqlQuery);
            $sqlStatement->execute([
                'nom_mot' => $word
            ]);
            $verifMot = $sqlStatement->fetchAll();
            if (count($verifMot) > 0) {
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

        for ($j = 0; $j < $nbLettres; $j++) {
            if (in_array(substr($word, $j), $tabVoyelle)) {
                $nbVoyelles++;
            }
            if (in_array(substr($word, $j), $tabLettreAccents)) {
                $nbCaracteresSpeciaux++;
            }
        }
        $i++;

        // Insertion du mot en bdd
        try {
            $sqlQuery = 'INSERT INTO mot (nom_mot, longueur_mot, nombre_voyelles, nombre_caracteres_speciaux) VALUES (:nom_mot, :nb_lettres, :nb_voyelles, :nb_caracteres_speciaux)';
            $sqlStatement = $mysqlClient->prepare($sqlQuery);
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
    echo "Importation réussie !";
}
?>

<!DOCTYPE html>
<html>

<head>
    <?php include_once('includes/head.php'); ?>
    <title>Import mots</title>
</head>

<body>
    <div class="main_div">
        <header><?php include_once('includes/header.php'); ?></header>
        <form action="" method="post" enctype="multipart/form-data">
            <h2>Importer des mots</h2>
            <label for="fileToUpload">Fichier à importer :</label>
            <input type="file" name="fileToUpload" id="fileToUpload">

            <input type="submit" value="Importer les mots" name="submit">
        </form>
    </div>
</body>

</html>