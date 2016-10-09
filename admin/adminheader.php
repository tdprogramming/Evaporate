<?php

require_once '../database/Session.php';
require_once '../database/CodeManager.php';
require_once '../database/AdminInstaller.php';

$adminInstaller = new AdminInstaller();

if (!$adminInstaller->alreadyInstalled()) {
    header("Location: install.php");
    die("Not yet installed. Redirecting you to the installer...");
}

$session = new Session();

if (!$session->isLoggedIn()) {
    header("Location: login.php");
    die("Redirecting you to the login page...");
}

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Admin Panel</title>
    </head>
    <link rel="stylesheet" href="../css/w3.css">
    <link rel="stylesheet" href="../css/links.css">
    <body>
        <div class="w3-container w3-blue">
            <span class="w3-left"><h2><a href="index.php">Admin Panel</a></h2></span>
            <span class="w3-right"><p><a class="w3-btn" href="logout.php">Log Out</a></p></span>
        </div>
    </body>
</html>