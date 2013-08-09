<?php

include 'config.inc.php';
$username = "autohomepi_remote"; //Username for OpenRemote
$password = "c1b06f7ef57e3c009837e9b732bfe4ac"; //Password for OpenRemote

if (isset($_POST['switch_id'])) {
    $switch_id = $_POST['switch_id'];
    if (isset($_POST['switch_status'])) {
        $switch_status = $_POST['switch_status'];
        $switch_status = $switch_status ? "on" : "off";
        // Write Switch
        $con = mysqli_connect($dbhost, $dbusername, $dbpassword, $dbname) or die();
        $query = "UPDATE `$autohomepi_db_dbname`.`switches` SET `switch_state`=\"" . $switch_status . "\" WHERE `switch_id` = '$switch_id'";
        $result = mysqli_query($con, $query) or die();
        mysqli_close($con);
        echo $result;
    }
} else if (isset($_GET['switch_id'])) {
    // Read Switch
    $con = mysqli_connect($dbhost, $dbusername, $dbpassword, $dbname) or die();
    $query = "SELECT `switch_state` FROM `$autohomepi_db_dbname`.`switches` WHERE `switch_id` = '$switch_id'";
    $result = mysqli_query($con, $query) or die();
    $row = mysqli_fetch_row($result);
    $switch_status = $row[0];
    mysqli_close($con);
    echo $switch_status;
} else {
    echo 'Error: 404 - Page not found!';
}
?>
