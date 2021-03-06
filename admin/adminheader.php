<?php

require_once '../database/Credentials.php';
require_once '../database/Session.php';
require_once '../database/CodeManager.php';
require_once '../database/AdminInstaller.php';

if (Credentials::adminOverHttps == TRUE && (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == "off")) {
    $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $redirect);
    exit();
}

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
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <body>
        <div class="w3-container w3-blue">
            <span class="w3-left"><a href="index.php"><i class="material-icons" style="font-size:48px;">home</i></a></span>
            <span class="w3-right"><p><a style="width: 180px;" href="logout.php"><i class="material-icons">exit_to_app</i></a></p></span>
            <span class="w3-right"><p><a style="width: 180px;" href="usersettings.php"><i class="material-icons">settings</i></a>&nbsp;</p></span>
        </div>
    </body>
</html>