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
    if ($_POST['formsendertype'] == "update_switch") {
        //get from $_POST ->: description, gpio, switch_id
        $switch_id_to_update = mysql_real_escape_string($_POST['switch_id']);
        $newDescription = mysql_real_escape_string($_POST["description" . $switch_id_to_update]);
        $newGpio = mysql_real_escape_string($_POST["gpio" . $switch_id_to_update]);
        $switchDisabled = isset($_POST["switch_disabled_" . $switch_id_to_update])?1:0;
            
        if (mysql_query("UPDATE `$dbname`.`switches` SET `switch_descr`='$newDescription',`switch_gpio`='$newGpio',`switch_disabled`=$switchDisabled WHERE `switch_id` = '$switch_id_to_update'")) {
            ?>
            <script type="text/javascript">
                var messenger = document.getElementsByClassName("messenger");
                messenger = messenger[0];
                messenger.id = "msg";
                messenger.innerHTML = "Switch \"<? echo $switch_id_to_update + 1; ?>\" updated successfully!";

            </script>
            <?
        } else {
            ?>
            <script type="text/javascript">
                var messenger = document.getElementsByClassName("messenger");
                messenger = messenger[0];
                messenger.id = "error";
                messenger.innerHTML = "Switch \"<? echo $switch_id_to_update + 1; ?>\" could not be updated!";
                alert("Switch \"<? echo $switch_id_to_update + 1; ?>\" could not be updated!");
            </script>
            <?
        }
    }


    $query = "SELECT * FROM `$dbname`.`switches`"; //fetch db for switches; switch_id, switch_state, switch_descr, switch_gpio, switch_disabled
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
                    <th rowspan="2"><label>S.No.</label></th>
                    <th rowspan="2"><label>Switch</label></th>
                    <th colspan="3" id="description"><label>Description</label></th>
                    <th rowspan="2" id="disable"><label>Disable</label></th>
                </tr>
                <tr>
                    <th id="gpio"><label>RPi.GPIO</label></th>
                    <th id="update"><label>Update</label></th>
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
                    $switch_disabled = $db_switch_data['switch_disabled'];
                    ?>

                    <tr align="center">
                        <td rowspan="2"><? echo $i + 1; ?></td>
                        <td rowspan="2"><img src="images/switch_<?
            if ($switch_disabled)
                echo 'disabled';
            else
                echo $switch_state; //switch state  
                    ?>.png" id="control_switch"></td>
                        <td id="description" colspan="3"><input id="descriptionfield" type="text" name="description<? echo $i; ?>" value="<? echo $switch_descr; ?>" <?if ($switch_disabled) echo"class=\"disabled\"";?>></td>
                        <td><input type="checkbox" id="disablebox" name="switch_disabled_<? echo $i; ?>" <?if($switch_disabled==1)echo 'checked';?>></td>
                    </tr>
                    <tr align="center">
                        <td id="gpio">
                            <select id="gpiofield" name="gpio<? echo $i; ?>">
                                <?
                                foreach ([11, 12, 13, 15, 16] as $pin) {
                                    ?><option label = "<? echo $pin;
                        if ($pin == $switch_gpio) echo '*'; ?>" value = "<? echo $pin; ?>"<? if ($pin == $switch_gpio) echo 'selected'; ?>><? echo $pin;
                        if ($pin == $switch_gpio) echo '*'; ?></option><?
                    }
                    ?>
                            </select>
                        </td>
                        <td id="update" colspan="2"><input type="button" name="<? echo $switch_id; ?>" value="Update" onclick="save_clickHandler(event);"></td>
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
                    $switch_disabled = $db_switch_data['switch_disabled'];
                    ?>
                    <tr align="center">
                        <td><? echo $i + 1; ?></td>
                        <td><input type="image" name="<? echo $i; ?>" value="Switch <? echo $i; ?>" src="images/switch_<?if ($switch_disabled) echo 'disabled'; else echo $switch_state; //switch state?>.png" id="control_switch" onclick="switch_clickHandler(event);" <?if($switch_disabled) echo"disabled";?>></td>
                        <td><? echo "$switch_descr"; //description        ?></td>
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