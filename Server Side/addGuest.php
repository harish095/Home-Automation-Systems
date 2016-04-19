<?php
    require_once "vendor/autoload.php";
    $firebase = new \Firebase\FirebaseLib("https://home-automation-system.firebaseio.com/");

    session_start();
    if(isset($_SESSION['username'])) {
        $username = $_SESSION['username'];
        $name = $_SESSION['name'];
        $type = $_SESSION['type'];
        if($type == "guest") {
            $expiry = $firebase->get("/guests/" . $_SESSION['username'] . "/expiry");
            if(time() > $expiry) {
                if(isset($_SESSION['username']))
                    unset($_SESSION['username']);
                if(isset($_SESSION['name']))
                    unset($_SESSION['name']);
                if(isset($_SESSION['type']))
                    unset($_SESSION['type']);
                header("Location: index.php?error=Guest%20session%20expired.");
            }
        }

        $devices = $firebase->get("/" . $type . "s/" . $username . "/devices");
        $devices = json_decode($devices, true);
    } else {
        header("Location: index.php");
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" class="no-js">
<head>
    <meta http-equiv = "Content-Type" content = "text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Harish Kandala">
    <meta name="description" content="Home Automation Systems is an IOT based project to control devices remotely">
    <meta name="keywords" content="IOT, Home Automation Systems, Internet, Control">
    <title>Home Automation Systems | Add Guest</title>
    <link rel="icon" href="img/favicon.ico">
    <!-----------------------------Stylesheets---------------------------->
    <link rel="stylesheet" type="text/css" href="include/css/styles.css">
    <!-------------------------------------------------------------------->
    <script src="include/js/init.js" type="text/javascript"></script>
</head>
<body>
<div class="navbar-fixed">
    <nav>
        <div class="nav-wrapper teal">
            <a href="index.php" style="font-weight: 600;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Home Automation Systems</a>
            <ul class="right hide-on-med-and-down">
                <li><a href="devices.php">Devices</a></li>
                <?php if($type != "guest") echo '<li><a href="guests.php">Guests</a></li>'; ?>
                <?php if($type != "guest") echo '<li><a href="addGuest.php" class="active">Add Guest</a></li>'; ?>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>
</div>

<div class="dark-wrapper">
    <div class="add-guest-wrapper card-panel">
        <h3>Add Guest</h3>
        <form action="#" id="addGuest-form">
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
            <div class="input-field">
                <i class="mdi-device-access-time prefix"></i>
                <input type="text" name="expiry" id="expiry"/>
                <label for="expiry">Expiry Time (in hours)</label>
            </div>
            <?php
                foreach($devices as $device => $value) {
                    echo '
                        <div class="device-item">
                            <input type="checkbox" class="filled-in" id="' . $device . '" name="' . $device . '"/>
                            <label for="' . $device . '">' . $device . '</label>
                        </div>';
                }
            ?>
            <input type="submit" class="btn-large" value="Add Guest"/>
        </form>
    </div>
</div>

<!--------------------------Scripts--------------------------------->
<script src="include/js/jquery-1.11.3.min.js" type="text/javascript"></script>
<script src="include/js/materialize.min.js" type="text/javascript"></script>
<script src="https://cdn.firebase.com/js/client/2.4.2/firebase.js"></script>
<script type="text/javascript">
    function addGuest() {
        var username = $("#username").val();
        var password = $("#password").val();
        var expiry = $("#expiry").val();
        var flag = 1;
        if(username == "") {
            flag = 0;
            Materialize.toast("Enter username of guest", 3000);
        } else {
            var guestRef = new Firebase("https://home-automation-system.firebaseio.com/guests/" + username);
            guestRef.on("value", function(snapshot) {
                var data = snapshot.val();
                if(data != null) {
                    flag = 0;
                    Materialize.toast("Username is not available", 3000);
                }
            });
        }
        if(password == "") {
            flag = 0;
            Materialize.toast("Enter password of guest", 3000);
        }
        if(!$.isNumeric(expiry)) {
            flag = 0;
            Materialize.toast("Enter valid expiry time", 3000);
        }

        expiry = Math.floor(new Date() / 1000) + (expiry*60*60);

        console.log(expiry);
    }

    $("#addGuest-form").submit(
        function (e) {
            e.preventDefault();
            addGuest();
        }
    )
</script>
<!------------------------------------------------------------------>
</body>
</html>