<?php

require_once 'database/FileManager.php';
require_once 'database/Session.php';

$session = new Session();
$fileManager = new FileManager();
$productId = filter_input(INPUT_GET, "productid", FILTER_SANITIZE_NUMBER_INT);

if ($productId != NULL) {
    $session->setSelectedProductId($productId);
} else {
    die ("Error - no selected product");
}

$filesArray = $fileManager->getFiles(FALSE);
$count = count($filesArray);

?>

<head>
    <meta charset="UTF-8">
    <title>Free Downloads</title>
</head>
<link rel="stylesheet" href="css/w3.css">
<body>
    <div class="w3-container w3-blue">
        <h2>Free Downloads</h2>
    </div>

    <div class="w3-container">
        <a class="w3-btn" href="index.php">Back to Products</a>
    </div>

    <div class="w3-container">
        <ul class="w3-ul w3-card-4">
            <?php
            for ($i = 0; $i < $count; $i++) {
            ?>
            <li class="w3-padding-16">
                <span class="w3-xlarge"><?php echo $filesArray[$i]["caption"] ?></span><br />
                <span><a class="w3-btn" href="downloadpremiumfile.php?filename=<?php echo $filesArray[$i]["filename"] ?>">Download</a>
            </li>
            <?php
            }
            ?>
        </ul>
    </div>
</body>