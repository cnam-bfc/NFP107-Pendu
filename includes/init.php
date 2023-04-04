<?php
// Code obligatoire, doit s'exécuter avant tout code html ou php
session_start();

// Récupération de la configuration
// Type de base de données
if (getenv('DATABASE_TYPE') !== false) {
    $database_type = getenv('DATABASE_TYPE');
} else {
    $database_type = 'mysql';
}

// Hôte de la base de données
if (getenv('DATABASE_HOST') !== false) {
    $database_host = getenv('DATABASE_HOST');
} else {
    $database_host = 'localhost';
}

// Port de la base de données
if (getenv('DATABASE_PORT') !== false) {
    $database_port = getenv('DATABASE_PORT');
} else {
    $database_port = '3306';
}

// Nom de la base de données
if (getenv('DATABASE_NAME') !== false) {
    $database_name = getenv('DATABASE_NAME');
} else {
    $database_name = 'cnam_nfp107_pendu';
}

// Charset de la base de données
if (getenv('DATABASE_CHARSET') !== false) {
    $database_charset = getenv('DATABASE_CHARSET');
} else {
    $database_charset = 'utf8';
}

// Utilisateur de la base de données
if (getenv('DATABASE_USERNAME') !== false) {
    $database_username = getenv('DATABASE_USERNAME');
} else {
    $database_username = 'cnam_nfp107_pendu';
}

// Mot de passe de la base de données
if (getenv('DATABASE_PASSWORD') !== false) {
    $database_password = getenv('DATABASE_PASSWORD');
} else {
    $database_password = '';
}

// FIREBIRD
// Rôle de l'utilisateur de la base de données
if (getenv('DATABASE_FIREBIRD_ROLE') !== false) {
    $database_role = getenv('DATABASE_FIREBIRD_ROLE');
}

// Configuration manuelle
/*
$database_type = 'firebird';
$database_host = 'localhost';
$database_port = '3050';
$database_name = 'C:\wamp64\www\Projet\SGBD\PENDU.FDB';
$database_charset = 'utf8';
$database_username = 'SYSDBA';
$database_password = 'masterkey';
// OPTIONNEL $database_role = '';
*/

// Connexion à la base de données
switch ($database_type) {
    case 'mysql':
        try {
            $DSN = 'mysql:host=' . $database_host . ';port=' . $database_port . ';dbname=' . $database_name . ';charset=' . $database_charset;
            $pdo = new PDO($DSN, $database_username, $database_password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (Exception $e) {
            $_SESSION['ERROR_MSG'] = 'Erreur de connexion à la base de données:</br>' . $e->getMessage();
            include_once('includes/error.php');
        }
        break;

    case 'firebird':
        try {
            $DSN = 'firebird:dbname=' . $database_host . '/' . $database_port . ':' . $database_name;
            if (!empty($database_role)) {
                $DSN .= ';role=' . $database_role;
            }
            $pdo = new PDO($DSN, $database_username, $database_password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Force column name to be lowercase
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
