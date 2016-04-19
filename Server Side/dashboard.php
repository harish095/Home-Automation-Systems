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
        $guests = $firebase->get("/" . $type . "s/" . $username . "/guests");
        if($guests != "null")
            $guests = json_decode($guests, true);
        else
            $guests = null;
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
    <title>Home Automation Systems | Dashboard</title>
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
                <?php if($type != "guest") echo '<li><a href="addGuest.php">Add Guest</a></li>'; ?>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>
</div>

<div class="dark-wrapper">
    <h2>Devices</h2>
    <?php
        $flag = 0;
        foreach($devices as $device => $value) {
            $status = $firebase->get("/devices/" . $device . "/status");
            if($status == "true") {
                $flag = 1;
                echo '
                    <div class="card-panel device clearfix">
                        <div class="left">'
                        . $device . '
                        </div>
                        <div class="btn red right" onclick="turnOff(\'' . $device . '\');">OFF</div>
                    </div>';
            }
        }
        if($flag == 0) {
            echo '
                <div class="card-panel device off">
                    All devices are turned off.
                </div>';
        }

        if($type != "guest") {
            echo '<h2>Active Guests</h2>';
            $flag = 0;
            foreach ($guests as $guest => $value) {
                $guestData = $firebase->get("/guests/" . $guest);
                $guestData = json_decode($guestData, true);
                if($guestData['active'] == true) {
                    if(time() < $guestData['expiry']) {
                        $flag = 1;
                        echo '
                            <div class="card-panel device clearfix">
                                <div class="left">'
                                    . $guestData['name'] . '
                                </div>
                                <div class="btn red right" onclick="revokeGuest(\'' . $guest . '\');">REVOKE</div>
                            </div>';
                    }
                }
            }
            if ($flag == 0) {
                echo '
                <div class="card-panel device off">
                    No Active guests found.
                </div>';
            }
        }
    ?>

</div>

<!--------------------------Scripts--------------------------------->
<script src="include/js/jquery-1.11.3.min.js" type="text/javascript"></script>
<script src="include/js/materialize.min.js" type="text/javascript"></script>
<script src="https://cdn.firebase.com/js/client/2.4.2/firebase.js"></script>
<script type="text/javascript">
    function turnOff(device) {
        var deviceData = new Firebase("https://home-automation-system.firebaseio.com/devices/" + device);
        deviceData.update({
            status: false
        }, function (error) {
            if(error) {
                Materialize.toast("Something wrong happened", 3000);
            } else {
                Materialize.toast("Turned Off", 3000);
            }
        });
    }
    function revokeGuest(guest) {
        var guestData = new Firebase("https://home-automation-system.firebaseio.com/guests/" + guest);
        guestData.update({
            active: false
        }, function (error) {
            if(error) {
                Materialize.toast("Something wrong happened", 3000);
            } else {
                Materialize.toast("Guest has been revoked", 3000);
            }
        });
    }
</script>
<!------------------------------------------------------------------>
</body>
</html>