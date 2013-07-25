<?php
    include 'config.inc.php';
    if(!(isset($_POST['Login'])&&isset($_POST['username'])&&isset($_POST['password'])))
    {
        header("Location:./?error=invalid_login"); 
        die();
    }
    else {
        $username = mysql_real_escape_string($_POST['username']);
        $password = md5(mysql_real_escape_string($_POST['password']));
        
        $con = mysql_connect($dbhost,$dbusername,$dbpassword);
        $query = "SELECT * FROM  `$dbname`.`users` WHERE `username` =  '$username'";
        $db_user_data = mysql_fetch_assoc(mysql_query($query));
        
        print_r($db_user_data);
        
        $user_type = $db_user_data['user_type'];      //from db
        $uid = $db_user_data['uid'];                  //from db
        $password_db = $db_user_data['password'];     //from db
        $name = $db_user_data['name'];
        
        
        if($password==$password_db) {
            session_start();
            $_SESSION['user']=$username;
            $_SESSION['user_type']=$user_type;    
            $_SESSION['uid']=$uid;
            $_SESSION['name']=$name;
            header("Location:./"); 
        }
        else {
            session_destroy();
            header("Location:./?error=invalid_login");
        }
    }
?>
