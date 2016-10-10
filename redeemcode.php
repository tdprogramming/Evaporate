<?php

require_once 'database/FileManager.php';
require_once 'database/CodeManager.php';
require_once 'database/Session.php';

$session = new Session();
$fileManager = new FileManager();
$codeManager = new CodeManager();
$productId = filter_input(INPUT_GET, "productid", FILTER_SANITIZE_NUMBER_INT);

if ($productId != NULL) {
    $session->setSelectedProductId($productId);
}

$validRedeem = FALSE;
$codeError = null;
$cmdRedeem = filter_input(INPUT_POST, "cmdredeem", FILTER_SANITIZE_NUMBER_INT);

if ($cmdRedeem != NULL) {
    $codeManager = new CodeManager();
    $selectedProductCode = filter_input(INPUT_POST, 'code', FILTER_SANITIZE_STRING);
    
    if ($codeManager->isCodeValid($selectedProductCode)) {
        $validRedeem = TRUE;
        $session->setSelectedProductCode($selectedProductCode);
    } else {
        $codeError = "Sorry, that code was invalid. Please try again.";
    }
}
?>
<head>
    <meta charset="UTF-8">
    <title>Redeem Code</title>
</head>
<link rel="stylesheet" href="css/w3.css">
<body>
    <?php
    if ($validRedeem) {
        ?>
    <div class="w3-container w3-blue">
        <h2>Premium Downloads</h2>
    </div>
        <?php
    } else if ($codeError) {
        ?>
    <div class="w3-container w3-red">
        <h2><?php echo $codeError; ?></h2>
    </div>
        <?php
    } else {
        ?>
    <div class="w3-container w3-blue">
        <h2>Redeem Code</h2>
    </div>
        <?php
    } ?>
    
    <div class="w3-container">
        <a class="w3-btn" href="index.php">Back to Products</a>
    </div>

    <div class="w3-container">
        <?php
        if ($validRedeem) {
            $productId = $session->getSelectedProductId();
            $filesArray = $fileManager->getFiles(TRUE);
            $count = count($filesArray);
        ?>
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
        <?php 
        } else {
            ?>
            <form class="w3-form" name="redeem-form" id="redeem-form" method="post" action="<?php 
                $selfURL = filter_input(INPUT_SERVER, "PHP_SELF", FILTER_SANITIZE_URL);
                echo $selfURL;
                ?>">
                <p>
                    <label class="w3-label">Code</label>
                    <input class="w3-input" tabindex="1" accesskey="c" name="code" type="text" maxlength="50" id="title" placeholder="Code">
                </p>
                
                <p>
                    <input class="w3-btn" tabindex="2" accesskey="r" type="submit" name="cmdredeem" value="Redeem"  />
                </p>
            </form>
        <?php
        } ?>
    </div>
</body>