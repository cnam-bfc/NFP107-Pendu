<?php
// Code obligatoire, doit s'exécuter avant tout code html ou php
session_start();
?>

<?php
// Connexion à la base de données
try {
    $mysqlClient = new PDO('mysql:host=10.254.0.10;dbname=cnam_nfp107_pendu;charset=utf8', 'cnam_nfp107_pendu', 'EZ3M38F9W)BY6]BF');
} catch (Exception $e) {
    $_SESSION['ERROR_MSG'] = 'Erreur de connexion à la base de données:</br>' . $e->getMessage();
    include_once('includes/error.php');
}
?>