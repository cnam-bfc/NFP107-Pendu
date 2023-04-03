<?php include_once('includes/init.php'); ?>

<?php
if (!isset($_SESSION['USER_LOGGED'])) {
    include_once('includes/redirect_backward.php');
    exit();
}

try {
    $sqlQuery = 'SELECT * FROM partie, mot WHERE id_utilisateur_partie = :id_utilisateur AND partie.id_mot_partie = mot.id_mot AND date_fin_partie IS NOT NULL ORDER BY date_fin_partie DESC';
    $sqlStatement = $pdo->prepare($sqlQuery);
    $sqlStatement->execute([
        'id_utilisateur' => $_SESSION['USER_ID']
    ]);
    $parties = $sqlStatement->fetchAll();
} catch (Exception $e) {
    $_SESSION['ERROR_MSG'] = 'Erreur lors de l\'éxécution de la requête SQL:</br>' . $e->getMessage();
    include_once('includes/error.php');
    exit();
}
?>
<!DOCTYPE html>
<html>

<head>
    <?php include_once('includes/head.php'); ?>
    <title>Mon compte</title>
    <link rel="stylesheet" href="css/account.css" />
</head>

<body>
    <div class="main_div">

        <header><?php include_once('includes/header.php'); ?></header>

        <div id="account_main_div">

            <h1>Bienvenue sur l'espace Mon compte, <?php echo htmlspecialchars($_SESSION['USER_PRENOM']) . ' ' . htmlspecialchars($_SESSION['USER_NOM']); ?></h1>

            <div id="account_container">

                <h2>Mes informations</h2>

                <div id="account_info">
                    <div id="account_info2">
                        <p><strong>Login :</strong></p>
                        <p><strong>Email :</strong></p>
                        <p><strong>Nom :</strong></p>
                        <p><strong>Prénom :</strong></p>
                    </div>

                    <div id="account_info3">

                        <P><?php echo htmlspecialchars($_SESSION['USER_LOGIN']); ?></p>
                        <P><?php echo htmlspecialchars($_SESSION['USER_EMAIL']); ?></p>
                        <P><?php echo htmlspecialchars($_SESSION['USER_NOM']); ?></p>
                        <P><?php echo htmlspecialchars($_SESSION['USER_PRENOM']); ?></p>

                    </div>
                </div>

                <a href="account_delete.php" id="account_delete">Supprimer mon compte</a>

            </div>

            <div id="account_partie">

                <h2>Mes parties</h2>

                <?php if (count($parties) == 0) : ?>
                    <p>Vous n'avez pas encore joué</p>
                <?php endif; ?>

                <h3 id="account_partie_create"><a href="partie_create.php" />Jouer une partie</h3>

                <?php foreach ($parties as $partie) : ?>
                    <a href="partie.php?id=<?php echo $partie['id_partie']; ?>" id="account_partie_link">
                        <div id="account_partie_container_1">
                            <h3><?php echo $partie['date_fin_partie'] . ' - ' . htmlspecialchars($partie['nom_mot']); ?></h3>

                            <p>Score : <?php echo htmlspecialchars($partie['score_partie']); ?></p>
                            <p>Longueur : <?php echo htmlspecialchars($partie['longueur_mot']); ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

        </div>
    </div>

    <footer><?php include_once('includes/footer.php'); ?></footer>
</body>

</html>