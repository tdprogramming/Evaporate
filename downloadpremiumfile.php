<?php

require_once 'database/FileManager.php';
require_once 'database/CodeManager.php';
require_once 'database/Session.php';

$session = new Session();
$codeManager = new CodeManager();
$fileManager = new FileManager();
$fileName = filter_input(INPUT_GET, "filename", FILTER_SANITIZE_URL);

if (!$codeManager->isCodeValid($session->getSelectedProductCode()) && !$fileManager->isFileFree($fileName)) {
    die("Invalid code");
}

$file = "downloads/product" . $session->getSelectedProductId() . "/" . $fileName;
$fp = fopen($file, 'rb');

header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=$fileName");
header("Content-Length: " . filesize($file));
fpassthru($fp);