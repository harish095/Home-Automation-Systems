<?php
    session_start();
    if(isset($_SESSION['username']))
        unset($_SESSION['username']);
    if(isset($_SESSION['name']))
        unset($_SESSION['name']);
    if(isset($_SESSION['type']))
        unset($_SESSION['type']);
    session_destroy();

    header("location: index.php");