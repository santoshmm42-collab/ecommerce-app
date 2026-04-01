<?php
// Redirect user directory pages to main pages
$page = basename($_SERVER['PHP_SELF']);

switch($page) {
    case 'index.php':
        header('Location: ../index.php');
        break;
    case 'shop.php':
        header('Location: ../shop.php');
        break;
    case 'categories.php':
        header('Location: ../categories.php');
        break;
    case 'contact.php':
        header('Location: ../contact.php');
        break;
    case 'about.php':
        header('Location: ../about.php');
        break;
    default:
        header('Location: ../index.php');
        break;
}
exit();
?>
