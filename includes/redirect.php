<!DOCTYPE html>
<html>

<head>
    <?php include_once('includes/head.php'); ?>
    <title>Redirection...</title>
    <script language="JavaScript" type="text/javascript">
        setTimeout(<?php echo ("\"window.location.href = '" . $_SESSION['REDIRECT_URL'] . "';\""); ?>, 100);
    </script>
</head>

<body>
    <p>Redirection...</br><a href="<?php echo ($_SESSION['REDIRECT_URL']); ?>">Cliquez ici</a></p>
</body>

</html>
<?php die(); ?>