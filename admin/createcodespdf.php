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

$batchId = filter_input(INPUT_GET, "batchid", FILTER_SANITIZE_NUMBER_INT);

if ($batchId != NULL) {
    $codeManager = new CodeManager();
    $codeManager->printCodes($batchId);    
}