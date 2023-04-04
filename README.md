# NFP107-Pendu

# Déployer le projet

## Via Docker

Build Docker image :

```bash
docker build -t nfp107-pendu:latest .
```

Deploy Docker container :

```bash
docker run -d \
  --name nfp107-pendu \
  -e DATABASE_TYPE=mysql \
  -e DATABASE_HOST=localhost \
  -e DATABASE_PORT=3306 \
  -e DATABASE_NAME=pendu \
  -e DATABASE_CHARSET=utf8 \
  -e DATABASE_USERNAME=root \
  -e DATABASE_PASSWORD= \
  -e DATABASE_FIREBIRD_ROLE=`#optional` \
  -p 8080:80 \
  --restart unless-stopped \
  nfp107-pendu:latest
```

## Via Wamp

Configurer les identifiants de la base de données dans le fichier includes/init.php,

Choisir le type de base de données entre 'mysql' et 'firebird', changer les autres champs au besoin

```php
$database_type = 'firebird';
$database_host = 'localhost';
$database_port = '3050';
$database_name = 'C:\wamp64\www\Projet\SGBD\PENDU.FDB';
$database_charset = 'utf8';
$database_username = 'SYSDBA';
$database_password = 'masterkey';
// OPTIONNEL $database_role = '';
```

# Créer une base de données

## MySQL / MariaDB

Importer le fichier CREATE DEFAULT MARIADB.sql dans votre base de données

## Firebird

Importer le fichier CREATE DEFAULT FIREBIRD.sql dans votre base de données


# Importer des mots

Pour importer des mots il faut être connecter avec l'utilisateur 'admin' et cliquer sur 'Importer' dans le header du site
