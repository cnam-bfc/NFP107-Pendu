<?php include_once('includes/init.php'); ?>

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
        <!-- Verifier si utilisateur est connecté -->
        <?php if (isset($_SESSION['USER_LOGGED']) && $_SESSION['USER_LOGGED'] == true) { ?>
            <!-- Si oui proposer Jouer ou Deconnexion -->
            <h2>Bienvenue <?php echo htmlspecialchars($_SESSION['USER_PRENOM']); ?></h2>
            <div id="button_index">
                <a href="partie_create.php">Jouer</a> <!-- Bouton / IMG ? on verra -->
                <a href="logout.php">Deconnexion</a> <!-- Bouton / IMG ? on verra -->
            </div>
        <?php } else { ?>
            <!-- Si "non" Proposer login ou créer compte -->
            <h2>Bienvenue sur CnamDU</h2>
            <div id="button_index">
                <a href="login.php">Connexion</a> <!-- Bouton / IMG ? on verra -->
                <a href="register.php">Inscription</a> <!-- Bouton / IMG ? on verra -->
            </div>
        <?php } ?>
        </div> <!-- center_div -->
    </div>
    <footer><?php include_once('includes/footer.php'); ?></footer>
</body>

</html>