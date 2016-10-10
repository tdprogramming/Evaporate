<?php

require_once 'database/FileManager.php';
require_once 'database/CodeManager.php';
require_once 'database/Session.php';

$session = new Session();
$codeManager = new CodeManager();

if (!$codeManager->isCodeValid($session->getSelectedProductCode())) {
    die("Invalid code");
}

$fileName = filter_input(INPUT_GET, "filename", FILTER_SANITIZE_URL);

$file = "downloads/premium/product" . $session->getSelectedProductId() . "/" . $fileName;
$fp = fopen($file, 'rb');

header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=$fileName");
header("Content-Length: " . filesize($file));
fpassthru($fp);