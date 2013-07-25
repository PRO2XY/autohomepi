<?php echo "Hello {$_SESSION['name']}!"; ?>

<form action="./" method="post" style="float: right;">
    <input type="submit" name="pswd_change" value="Change Password">
    <input type="submit" name="logout" value="Logout">
</form>
<div style="clear: both;"></div>

<?
$con = mysql_connect($dbhost, $dbusername, $dbpassword) or die($errormsg);

if (isset($_POST['pswd_change'])) {
    ?>
    <form method="post" action="./">
        <input type="hidden" name="formsendertype" value="passwordchanger">
        <table>
            <caption>Change Password for <? echo $_SESSION['username']; ?></caption>
            <tr><td>Current password:</td><td><input type="password" name="cur_password" required></td></tr>
            <tr><td>New password:</td><td><input type="password" name="new_password1" required></td></tr>
            <tr><td>Re-enter new password:</td><td><input type="password" name="new_password2" required></td></tr>
            <tr><td></td><td><input type="submit" name="submit" value="Submit"></td></tr>
        </table>
    </form>
    <?
} else if (isset($_POST['formsendertype']) && $_POST['formsendertype'] == "passwordchanger") {   //Password changer
    $cur_pswd = $_POST['cur_password'];
    $new_pswd1 = $_POST['new_password1'];
    $new_pswd2 = $_POST['new_password2'];
    $res = mysql_fetch_assoc(mysql_query("SELECT `password` FROM `$dbname`.`users` WHERE `username` = '{$_SESSION['user']}'"));
    $pswd_in_db = $res['password'];
    if (!(($new_pswd1 == $new_pswd2) && (md5($cur_pswd) == $pswd_in_db))) {     //unauthorised or passwords don't match
        header("Location:./?error=pswdnomatch");
    } else {
        $new_pswd = md5($new_pswd1);
        mysql_query("UPDATE `$dbname`.`users` SET `password`='$new_pswd' WHERE `username`='{$_SESSION['user']}'") or die($errormsg);
        header("Location:./?msg=pswdchangesuccess");
    }
} else if (isset($_POST['formsendertype']) && ($_POST['formsendertype'] == "edit" || $_POST['formsendertype'] == "update_switch")) {   //edit button pressed
    if ($_POST['formsendertype']=="update_switch") {
        //get from $_POST ->: description, gpio, switch_id
        $switch_id_to_update = mysql_real_escape_string($_POST['switch_id']);
        $newDescription = mysql_real_escape_string($_POST["description".$switch_id_to_update]);
        $newGpio = mysql_real_escape_string($_POST["gpio".$switch_id_to_update]);

        mysql_query("UPDATE `$dbname`.`switches` SET `switch_descr`='$newDescription',`switch_gpio`='$newGpio' WHERE `switch_id` = '$switch_id_to_update'") or die($errormsg);

    }
    
    
    $query = "SELECT * FROM `$dbname`.`switches`"; //fetch db for switches; switch_id, switch_state, switch_descr, switch_gpio
    $db_switch_result = mysql_query($query);
    $rows = mysql_num_rows($db_switch_result);
    ?>
    <form method="post" action="./" id="update_form">
        <input type="hidden" name="formsendertype" value="update_switch">
        <input type="hidden" name="switch_id" id="switchidtoupdate">
        
        <table id="control">
            <caption> autoHomePi Control Panel Edit </caption>
            <thead>
                <tr>
                    <th><label>S.No.</label></th>
                    <th><label>Switch</label></th>
                    <th><label>Description</label></th>
                    <th><label>RPi.GPIO</label></th>
                </tr>
            </thead>
            <tbody>
                <?
                for ($i = 0; $i < $rows; $i++) {
                    $db_switch_data = mysql_fetch_assoc($db_switch_result);
                    $switch_id = $db_switch_data['switch_id'];
                    $switch_state = $db_switch_data['switch_state'];
                    $switch_descr = $db_switch_data['switch_descr'];
                    $switch_gpio = $db_switch_data['switch_gpio'];
                    ?>

                    <tr>
                        <td><? echo $i + 1; ?></td>
                        <td><img src="images/switch_<? echo $switch_state; //switch state     ?>.png" id="control_switch"></td>
                        <td><input type="text" name="description<? echo $i; ?>" value="<? echo $switch_descr; ?>"></td>
                        <td><input type="number" name="gpio<? echo $i; ?>" value="<? echo $switch_gpio; ?>"></td>
                        <td><input type="button" name="<? echo $switch_id; ?>" value="Update" onclick="save_clickHandler(event);"></td>
                    </tr>
                <? } ?>
                <tr><td></td><td></td><td><a href="./"><-- Go back</a></td></tr>
            </tbody>
        </table>
    </form>
    <?
} else {
    if (isset($_POST['formsendertype']) && $_POST['formsendertype'] == "switch") {      //switch pressed
        $switch_id = $_POST['formsenderid'];
        $dbswstate = mysql_fetch_assoc(mysql_query("SELECT `switch_state` FROM `$dbname`.`switches` WHERE `switch_id` = '$switch_id'"));
        if ($dbswstate['switch_state'] == "off") {
            mysql_query("UPDATE `$dbname`.`switches` SET `switch_state`=\"on\" WHERE `switch_id` = '$switch_id'") or die($errormsg);
        } else {
            mysql_query("UPDATE `$dbname`.`switches` SET `switch_state`=\"off\" WHERE `switch_id` = '$switch_id'") or die($errormsg);
        }
    }
    $query = "SELECT * FROM `$dbname`.`switches`"; //fetch db for switches; switch_id, switch_state, switch_descr, switch_gpio
    $db_switch_result = mysql_query($query);
    $rows = mysql_num_rows($db_switch_result);
    ?>
    <form method="post" action="./" id="control_form">
        <input type="hidden" id="sendertype" name="formsendertype">
        <input type="hidden" id="senderid" name="formsenderid">
        <table id="control">
            <caption> autoHomePi Control Panel </caption>
            <thead>
                <tr>
                    <th><label for=e1>S.No.</label></th>
                    <th><label for=e2>Switch</label></th>
                    <th><label for=e3>Description</label></th>
                    <?
                    if ($_SESSION['user_type'] == "admin") {
                        echo '<th><label for=e4>Edit</label></th>';
                    }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?
                for ($i = 0; $i < $rows; $i++) {
                    $db_switch_data = mysql_fetch_assoc($db_switch_result);
                    $switch_id = $db_switch_data['switch_id'];
                    $switch_state = $db_switch_data['switch_state'];
                    $switch_descr = $db_switch_data['switch_descr'];
                    ?>
                    <tr>
                        <td><? echo $i + 1; ?></td>
                        <td><input type="image" name="<? echo $i; ?>" value="Switch <? echo $i; ?>" src="images/switch_<? echo $switch_state; //switch state     ?>.png" id="control_switch" onclick="switch_clickHandler(event);"></td>
                        <td><? echo "$switch_descr"; //description     ?></td>
                        <? if ($_SESSION['user_type'] == "admin") { ?><td><input type="button" name="<? echo $i; ?>" value="Edit" id="control_edit" onclick="edit_clickHandler(event);"></td><? } ?>
                    </tr>
                <? } ?>
            </tbody>
        </table>
    </form>
    <?
    mysql_close($con);
}
?>
<script>
                    function switch_clickHandler(event) {
                        document.getElementById('sendertype').value = "switch";
                        document.getElementById('senderid').value = event.target.name;
                        document.getElementById('control_form').submit();
                    }
                    function edit_clickHandler(event) {
                        document.getElementById('sendertype').value = "edit";
                        document.getElementById('senderid').value = event.target.name;
                        document.getElementById('control_form').submit();
                    }
                    function save_clickHandler(event) {
                        document.getElementById('switchidtoupdate').value = event.target.name;
                        document.getElementById('update_form').submit();
                    }
</script>