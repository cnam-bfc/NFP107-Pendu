<div id="header_main_div">
    <div id="header_logo">
        <a href="./">
            <img src="res/img/CNAM_Logo.jpg" alt="Logo de CnamDu" />
        </a>
        <a href="./">
            <h1>CnamDu</h1>
        </a>
    </div>
    <nav>
        <ul id="header_menu">
            <li><a href="./">Accueil</a></li> <!-- A COMPLETER pour changer de pseudo, revenir page acceuil -->
            <li><a href="classement.php">Classement</a></li> <!-- A COMPLETER pour voir le classement -->
            <li><a href="import.php">Importer</a></li>
            <?php if (!isset($_SESSION['USER_LOGGED'])) : ?>
                <li><a href="login.php">Connexion</a></li>
                <li><a href="register.php">Inscription</a></li>
            <?php else : ?>
                <li><a href="account.php"><?php echo htmlspecialchars($_SESSION['USER_PRENOM']) . ' ' . htmlspecialchars($_SESSION['USER_NOM']); ?></a></li>
                <li><a href="logout.php">Déconnexion</a></li>
            <?php endif; ?>
        </ul>
</div>