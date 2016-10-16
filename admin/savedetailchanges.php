<?php

require_once '../database/Session.php';
require_once '../database/ProductManager.php';
require_once '../database/FileManager.php';
require_once '../database/CodeManager.php';

$session = new Session();

if (!$session->isLoggedIn())
{
    die ("Not logged in.");
}

$productManager = new ProductManager();
$productManager->fetchCurrentProduct();

$title = filter_input(INPUT_POST, "title", FILTER_SANITIZE_STRING);
$description = filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING);
$orderLink = filter_input(INPUT_POST, "orderlink", FILTER_SANITIZE_URL);
$redeemLink = filter_input(INPUT_POST, "redeemlink", FILTER_SANITIZE_URL);
    
$productManager->updateCurrentProduct($title, $description, $orderLink, $redeemLink);

$fileManager = new FileManager();

$numFileCaptions = filter_input(INPUT_POST, "numfilecaptions", FILTER_SANITIZE_NUMBER_INT);

for ($i = 0; $i < $numFileCaptions; $i++) {
    $fileId = filter_input(INPUT_POST, "fileid" . $i, FILTER_SANITIZE_NUMBER_INT);
    $fileCaption = filter_input(INPUT_POST, "filecaption" . $i, FILTER_SANITIZE_STRING);
    $fileManager->updateFileCaption($fileId, $fileCaption);
}

$codeManager = new CodeManager();

$numBatchCaptions = filter_input(INPUT_POST, "numbatchcaptions", FILTER_SANITIZE_NUMBER_INT);

for ($i = 0; $i < $numBatchCaptions; $i++) {
    $batchId = filter_input(INPUT_POST, "batchid" . $i, FILTER_SANITIZE_NUMBER_INT);
    $batchCaption = filter_input(INPUT_POST, "batchcaption" . $i, FILTER_SANITIZE_STRING);
    $codeManager->updateBatchName($batchId, $batchCaption);
}