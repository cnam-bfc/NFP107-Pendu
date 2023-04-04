<?php
// Code obligatoire, doit s'exécuter avant tout code html ou php
session_start();

define('DATABASE_TYPE', 'firebird');

// Connexion à la base de données
switch (DATABASE_TYPE) {
    case 'mysql':
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=cnam_nfp107_pendu;charset=utf8', 'cnam_nfp107_pendu', 'EZ3M38F9W)BY6]BF');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (Exception $e) {
            $_SESSION['ERROR_MSG'] = 'Erreur de connexion à la base de données:</br>' . $e->getMessage();
            include_once('includes/error.php');
        }
        break;

    case 'firebird':
        $host = "localhost";
        // $database = "C:\wamp64\www\Projet\SGBD\PENDU.FDB";
        $database = "pendu";
        // $user = "SYSDBA";
        $user = "PENDU";
        // $password = "masterkey";
        $password = "pendu";
        $role = "PENDUROLE";

        try {
            $DSN = "firebird:dbname=$host:$database";
            if (!empty($role)) {
                $DSN .= ";role=$role";
            }
            $pdo = new PDO($DSN, $user, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Set column name lowercase
            $pdo->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
        } catch (PDOException $e) {
            $_SESSION['ERROR_MSG'] = 'Erreur de connexion à la base de données:</br>' . $e->getMessage();
            include_once('includes/error.php');
        }
        break;
    default:
        $_SESSION['ERROR_MSG'] = 'Erreur de connexion à la base de données:</br>Type de base de données inconnu';
        include_once('includes/error.php');
        break;
}
