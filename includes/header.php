<div id="header_main_div">
    <!-- logo cnam -->
    <a href="https://lecnam.net/"> <!-- Quand on clique sur l'image on pop sur le site du cnam-->
        <img src="res/img/logoCnam.png" alt="logo_cnam" id="logo_cnam">
    </a>
    <div id="section_centrale">
        <a href="index.php"> <!-- Quand on clique sur les images ou Accueuil on revient à index-->
            <h1>CnamDu</h1>
        </a>
    </div>
    <nav>
        <ul>
            <li><a href="..\index.php">Acceuil</a></li> <!-- A COMPLETER pour changer de pseudo, revenir page acceuil -->
            <li><a href="..\classement.php">Classement</a></li> <!-- A COMPLETER pour voir le classement -->
            <?php if (!isset($_SESSION['USER_LOGGED'])) : ?>
                <li><a href="login.php">Connexion</a></li>
                <li><a href="register.php">Inscription</a></li>
            <?php else : ?>
                <li><a href="account.php"><?php echo htmlspecialchars($_SESSION['USER_PRENOM']) . ' ' . htmlspecialchars($_SESSION['USER_NOM']); ?></a></li>
                <li><a href="logout.php">Déconnexion</a></li>
            <?php endif; ?>
        </ul>
</div>