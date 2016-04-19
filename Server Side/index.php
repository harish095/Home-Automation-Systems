<?php
    require_once "vendor/autoload.php";
    $firebase = new \Firebase\FirebaseLib("https://home-automation-system.firebaseio.com/");

    session_start();
    if (isset($_SESSION['username'])) {
        header("Location: dashboard.php");
    }

    if (isset($_GET['error'])) {
        $error = $_GET['error'];
    } else {
        $error = null;
    }

    if (isset($_POST['username']) && isset($_POST['password'])) {
        if ($_POST['password'] == null) {
            $error = "Please enter your password";
        }
        if ($_POST['username'] == null) {
            $error = "Please enter your username";
        }
        if (isset($_POST['guest']) && $_POST['guest']) {
            $path = "/guests";
            $type = "guest";
        } else {
            $path = "/owners";
            $type = "owner";
        }
        if ($_POST['username'] != null && $_POST['password'] != null) {
            $path .= '/' . $_POST['username'];
            $userData = $firebase->get($path);
            if($userData != "null") {
                $user = json_decode($userData, true);
                if($user['password'] == $_POST['password']) {
                    $_SESSION['username'] = $_POST['username'];
                    $_SESSION['name'] = $user['name'];
                    $_SESSION['type'] = $type;
                    header("Location: dashboard.php");
                } else {
                    $error = "Wrong password, Please try again.";
                }
            } else {
                $error = "User not found.";
            }
        }
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" class="no-js">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Harish Kandala">
    <meta name="description" content="Home Automation Systems is an IOT based project to control devices remotely">
    <meta name="keywords" content="IOT, Home Automation Systems, Internet, Control">
    <title>Home Automation Systems | Login</title>
    <link rel="icon" href="img/favicon.ico">
    <!-----------------------------Stylesheets---------------------------->
    <link rel="stylesheet" type="text/css" href="include/css/styles.css">
    <!-------------------------------------------------------------------->
    <script src="include/js/init.js" type="text/javascript"></script>
</head>
<body>
<div class="wrapper">
    <div class="login-wrapper card-panel z-depth-2">
        <h3>Login</h3>
        <form action="index.php" method="post">
            <div class="input-field">
                <i class="mdi-action-account-circle prefix"></i>
                <input type="text" name="username" id="username"/>
                <label for="username">Username</label>
            </div>
            <div class="input-field">
                <i class="mdi-communication-vpn-key prefix"></i>
                <input type="password" name="password" id="password"/>
                <label for="password">Password</label>
            </div>
            <div class="guest-check">
                <input type="checkbox" class="filled-in" id="guest" name="guest"/>
                <label for="guest">Are you a Guest?</label>
            </div>
            <input type="submit" class="btn-large" value="Login"/>
        </form>
    </div>
</div>

<!--------------------------Scripts--------------------------------->
<script src="include/js/jquery-1.11.3.min.js" type="text/javascript"></script>
<script src="include/js/materialize.min.js" type="text/javascript"></script>
<script type="text/javascript">
    <?php
        if ($error != null) {
            echo 'Materialize.toast("' . $error . '", 10000);';
        }
    ?>
</script>
<!------------------------------------------------------------------>
</body>
</html>