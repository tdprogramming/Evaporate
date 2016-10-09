<?php

require_once "../libs/fpdf.php";
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

if (isset($_GET["batchid"])) {
    $codeManager = new CodeManager();
    $codeManager->printCodes($_GET["batchid"]);    
}