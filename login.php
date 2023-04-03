<?php include_once('includes/init.php'); ?>

<?php
if (isset($_SESSION['USER_LOGGED'])) {
    include_once('includes/redirect_backward.php');
    exit();
}

if (count($_POST) != 0) {
    if (
        empty($_POST['identifiant'])
        || (strpos($_POST['identifiant'], '@') && !filter_var($_POST['identifiant'], FILTER_VALIDATE_EMAIL))
        || empty($_POST['password'])
    ) {
        $_SESSION['ERROR_MSG'] = 'Informations fournies non valides !';
        include_once('includes/error.php');
        exit();
    }

    $identifiant = $_POST['identifiant'];
    $password = $_POST['password'];

    // On récupère l'utilisateur dans la base de données
    try {
        $sqlQuery = 'SELECT * FROM utilisateur WHERE login_utilisateur = :login_utilisateur OR email_utilisateur = :email_utilisateur';
        $sqlStatement = $pdo->prepare($sqlQuery);
        $sqlStatement->execute([
            'login_utilisateur' => $identifiant,
            'email_utilisateur' => $identifiant
        ]);
        $utilisateurs = $sqlStatement->fetchAll();
    } catch (Exception $e) {
        $_SESSION['ERROR_MSG'] = 'Erreur lors de l\'éxécution de la requête SQL:</br>' . $e->getMessage();
        include_once('includes/error.php');
        exit();
    }

    foreach ($utilisateurs as $utilisateur) {
        if (!password_verify($password, $utilisateur['password_utilisateur'])) {
            continue;
        }
        $id = $utilisateur['id_utilisateur'];
        $login = $utilisateur['login_utilisateur'];
        $email = $utilisateur['email_utilisateur'];
        $nom = $utilisateur['nom_utilisateur'];
        $prenom = $utilisateur['prenom_utilisateur'];
    }

    if (!isset($login)) {
        $_SESSION['ERROR_MSG'] = 'Identifiant ou mot de passe incorrect';
        include_once('includes/error.php');
        exit();
    }

    // On sauvegarde les informations de l'utilisateur dans la session
    $_SESSION['USER_LOGGED'] = true;
    $_SESSION['USER_ID'] = $id;
    $_SESSION['USER_LOGIN'] = $login;
    $_SESSION['USER_EMAIL'] = $email;
    $_SESSION['USER_NOM'] = $nom;
    $_SESSION['USER_PRENOM'] = $prenom;

    include_once('includes/redirect_backward.php');
    exit();
}
?>
<!DOCTYPE html>
<html>

<head>
    <?php include_once('includes/head.php'); ?>
    <title>Connexion</title>
    <link rel="stylesheet" href="css/auth.css" />
</head>

<body>
    <div class="main_div">
        <header><?php include_once('includes/header.php'); ?></header>

        <div id="auth_main_div">
            <form action="" method="POST" id="auth_form">
                <h1>Connexion</h1>

                <label for="identifiant">Identifiant</label>
                <input type="text" name="identifiant" autofocus required placeholder="Email / Login" />

                <label for="password">Mot de passe</label>
                <input type="password" name="password" required placeholder="Mot de passe" />

                <div id="auth_button_div">
                    <input type="submit" value="Se connecter" id="auth_buttom" /></br>
                    <a href="register.php">Pas encore inscrit?</a>
                </div>
            </form>
        </div>
    </div>

    <footer><?php include_once('includes/footer.php'); ?></footer>
</body>

</html>