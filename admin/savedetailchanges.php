<?php

require_once '../database/Session.php';
require_once '../database/ProductManager.php';

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