<?php
include 'config.inc.php';

//if (isset($_POST['username']) && isset($_POST['password']) && $_POST['username'] == $ORusername && $_POST['password'] == $ORpassword) {
    if (isset($_POST['switch_id'])) {
        $switch_id = $_POST['switch_id'];
        if (isset($_POST['switch_status'])) {
            $switch_status = $_POST['switch_status'];
            $switch_status = $switch_status ? "on" : "off";
            // Write Switch
            $con = mysqli_connect($dbhost, $dbusername, $dbpassword, $dbname) or die();
            $query = "UPDATE `$dbname`.`switches` SET `switch_state`=\"" . $switch_status . "\" WHERE `switch_id` = '$switch_id'";
            $result = mysqli_query($con, $query) or die("Failed");
            mysqli_close($con);
            echo $switch_status;
        }
    } else if (isset($_GET['switch_id'])) {
        // Read Switch
	$switch_id = $_GET['switch_id'];
        $con = mysqli_connect($dbhost, $dbusername, $dbpassword, $dbname) or die();
        $query = "SELECT `switch_state` FROM `$dbname`.`switches` WHERE `switch_id` = '$switch_id'";
        $result = mysqli_query($con, $query) or die();
        $row = mysqli_fetch_row($result);
        $switch_status = $row[0];
        mysqli_close($con);
        echo $switch_status;
    } else {
        echo 'Error: 404 - Page not found!';
    }
//} else {
//    echo 'Error: 404 - Page not found!';
//}
?>