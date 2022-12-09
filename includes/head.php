<meta charset="UTF-8" />
<!-- Import des feuilles de style -->
<link rel="stylesheet" href="css/reset.css" />
<link rel="stylesheet" href="css/font.css" />
<link rel="stylesheet" href="css/header.css" />
<link rel="stylesheet" href="css/footer.css" />
<link rel="stylesheet" href="css/main.css" />
<?php if (isset($_SESSION['REFRESH_PAGE']) && $_SESSION['REFRESH_PAGE'] > 0) : ?>
    <?php if ($_SESSION['REFRESH_PAGE'] == 1) : ?>
        <meta http-equiv="refresh" content="0">
    <?php endif; ?>
    <?php $_SESSION['REFRESH_PAGE']--; ?>
<?php endif; ?>