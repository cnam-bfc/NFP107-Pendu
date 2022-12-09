<?php include_once('includes/init.php'); ?>

<?php
if (isset($_SESSION['USER_LOGGED'])) {
    include_once('includes/redirect_backward.php');
}

if (count($_POST) != 0) {
    if (
        empty($_POST['email'])
        || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)
        || empty($_POST['login'])
        || strpos($_POST['login'], '@') !== false
        || empty($_POST['nom'])
        || empty($_POST['prenom'])
        || empty($_POST['password'])
        || empty($_POST['password_comfirm'])
    ) {
        $_SESSION['ERROR_MSG'] = 'Informations fournies non valides !';
        include_once('includes/error.php');
        exit();
    }

    $email = $_POST['email'];
    $login = $_POST['login'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $password = $_POST['password'];
    $password_comfirm = $_POST['password_comfirm'];

    if ($password != $password_comfirm) {
        $_SESSION['ERROR_MSG'] = 'Les deux mot de passe ne sont pas identique';
        include_once('includes/error.php');
        exit();
    }

    // On vérifie si il existe déjà un utilisateur avec ce login
    try {
        $sqlQuery = 'SELECT login_utilisateur FROM utilisateur WHERE login_utilisateur = :login';
        $sqlStatement = $mysqlClient->prepare($sqlQuery);
        $sqlStatement->execute([
            'login' => $login
        ]);
        $logins = $sqlStatement->fetchAll();
        if (count($logins) > 0) {
            $_SESSION['ERROR_MSG'] = "Le login \"" . $login . "\" existe déjà";
            include_once('includes/error.php');
            exit();
        }
    } catch (Exception $e) {
        $_SESSION['ERROR_MSG'] = 'Erreur lors de l\'éxécution de la requête SQL:</br>' . $e->getMessage();
        include_once('includes/error.php');
        exit();
    }

    // On vérifie si il existe déjà un utilisateur avec cet email
    try {
        $sqlQuery = 'SELECT email_utilisateur FROM utilisateur WHERE email_utilisateur = :email';
        $sqlStatement = $mysqlClient->prepare($sqlQuery);
        $sqlStatement->execute([
            'email' => $email
        ]);
        $emails = $sqlStatement->fetchAll();
        if (count($emails) > 0) {
            $_SESSION['ERROR_MSG'] = "L'email \"" . $email . "\" existe déjà";
            include_once('includes/error.php');
            exit();
        }
    } catch (Exception $e) {
        $_SESSION['ERROR_MSG'] = 'Erreur lors de l\'éxécution de la requête SQL:</br>' . $e->getMessage();
        include_once('includes/error.php');
        exit();
    }

    // Tout est OK on ajoute l'utilisateur
    try {
        $sqlQuery = 'INSERT INTO utilisateur (email_utilisateur, login_utilisateur, password_utilisateur, prenom_utilisateur, nom_utilisateur) VALUES (:email, :login, :password, :prenom, :nom)';
        $sqlStatement = $mysqlClient->prepare($sqlQuery);
        $sqlStatement->execute([
            'email' => $email,
            'login' => $login,
            'nom' => $nom,
            'prenom' => $prenom,
            'password' => password_hash($password, PASSWORD_BCRYPT)
        ]);
    } catch (Exception $e) {
        $_SESSION['ERROR_MSG'] = 'Erreur lors de l\'éxécution de la requête SQL:</br>' . $e->getMessage();
        include_once('includes/error.php');
        exit();
    }

    // On login l'utilisateur
    // On récupère l'utilisateur dans la base de données
    try {
        $sqlQuery = 'SELECT * FROM utilisateur WHERE login_utilisateur = :identifiant OR email_utilisateur = :identifiant';
        $sqlStatement = $mysqlClient->prepare($sqlQuery);
        $sqlStatement->execute([
            'identifiant' => $identifiant
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
    <title>Inscription</title>
    <link rel="stylesheet" href="css/auth.css" />
</head>

<body>
    <div class="main_div">
        <header><?php include_once('includes/header.php'); ?></header>

        <div id="auth_main_div">

            <form action="" method="POST" id="auth_form">

                <h1>Inscription</h1>

                <label for="email">Email</label>
                <input type="email" name="email" autofocus required placeholder="Email" />

                <label for="login">Login</label>
                <input type="text" name="login" required placeholder="Login" />

                <label for="nom">Nom</label>
                <input type="text" name="nom" required placeholder="Nom" />

                <label for="prenom">Prénom</label>
                <input type="text" name="prenom" required placeholder="Prénom" />

                <label for="password">Mot de passe</label>
                <input type="password" name="password" required placeholder="Mot de passe" />
                <input type="password" name="password_comfirm" required placeholder="Confirmer mot de passe" />

                <div id="auth_button_div">
                    <input type="submit" value="S'inscrire" id="auth_buttom" /></br>
                    <a href="login.php">Déjà inscrit?</a>
                </div>

            </form>

        </div>
    </div>

    <footer><?php include_once('includes/footer.php'); ?></footer>
</body>

</html>