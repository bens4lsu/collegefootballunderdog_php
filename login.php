<?php
	include_once ('./includes/classDb.php');
    include_once ('./includes/classPerson.php');
    include_once ('./includes/fnRedirect.php');
    $db = new Db();
    $failText = false;
	
	if (isset($_POST['submitted']) && $_POST['submitted'] == 1){
		$U1 = $db->cbSqlQuery('select idPoolUsers from PoolUsers where EmailAddress = \''.$_POST['user'].'\' and Password = \''.$_POST['password'].'\'');
        if (! is_bool($U1)){
            // login succesful.  create a Person class for the user, and save as a session variable.
            $user = new Person($U1['idPoolUsers'], $db);
            session_start();
            if(isset($_SESSION['user'])){
                unset($_SESSION['user']);
            }
            $user->unsetDb();
            $_SESSION['user']=serialize($user);
            cbRedirect('./');
        }
        else {
            $failText = true;
        }
    }
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US" xml:lang="en-US">
<head>

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="language" content="en" />

    <title>A Little Something to Make It Interesting</title>
    <link rel="stylesheet" href="style.css" type="text/css" media="screen" />
    <meta name='robots' content='index,follow' />
    <script type="text/javascript" src="../ckeditor/ckeditor.js"></script>

    <style>
        .heading h2 {padding-left:1em;}
        .loginbox table, .loginbox table td {border:0; padding-bottom:10px;}

    </style>

</head>

<body>

    <div class="main">
        <div class="heading"><h2>Login</h2></div>
        <div class="content">

            

            <div class="loginbox">
                <?php
                    if ($failText){
                        print '<div style="font-weight:bold; color:red">There was an error authenticating using the credentials supplied.  Please try again, or contact your game\'s commisioner.</div>';
                    }
                ?>
                <form action="login.php" method="post">
                    <input type="hidden" name="submitted" value="1" />
                    <table>
                    <tr><td>User Name:</td><td><input type="text" name="user" id="user" size="50" /></td></tr>
                    <tr><td>Password:</td><td><input type="password" name="password" id="password" size="50" /></td></tr>
                    <tr><td>&nbsp;</td><td><input type="submit" value="Login" /></td></tr>
                <form>
            </div>

</body>
</html>