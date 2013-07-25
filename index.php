<?php session_start();
include 'config.inc.php';
if(isset($_POST['logout']))
{
    session_unset();
    session_destroy();
    header("Location:./");
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="Anand P Pathak">
        <meta name="author" content="Pranav Sharma">
        <title>autoHomePi - A Home Automation project on Raspberry Pi!</title>
        <link rel="stylesheet" type="text/css" href="style.css">
    </head>
    <body>
        <div id="wrapper">
            <?php
            if (isset($_GET['error'])) {
                switch ($_GET['error']) {
                    case 'invalid_login':
                        echo '<div id="error">Invalid login credentials!</div>';
                        break;
                    case 'pswdnomatch':
                        echo '<div id="error">Unauthorised access or passwords don\'t match!</div>';
                        break;
                }
            }
            if (isset($_GET['msg'])) {
                switch ($_GET['msg']) {
                    case 'pswdchangesuccess':
                        echo '<div id="msg">Password successfully changed!</div>';
                        break;
                }
            }
            if (!isset($_SESSION['user'])) {  //not logged in, show login screen
                include_once 'login.inc.php';
                die();
            } else {
                include 'control.inc.php';
            }
            ?>
        </div>
    </body>
</html>
