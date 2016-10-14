<?php

require_once '../database/FileManager.php';
require_once '../database/ProductManager.php';
require_once '../database/Session.php';
require_once 'adminheader.php';

$productManager = new ProductManager();
$fileManager = new FileManager();

if (isset($_GET["productid"])) {
    $productId = filter_input(INPUT_GET, "productid", FILTER_SANITIZE_NUMBER_INT);
    $session->setSelectedProductId($productId);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_FILES["file"])) {
    echo "Got file";
    // TODO: validate upload file name
    $selectedProductId = $session->getSelectedProductId();
    $uploaddir = '../downloads/product' . $selectedProductId . "/";
    $finalName = basename($_FILES['file']['name']);
    $uploadfile = $uploaddir . $finalName;
        
    // Note - need appropriate file size limits here
    if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {
        $caption = filter_input(INPUT_POST, "caption", FILTER_SANITIZE_STRING);
        // Insert the file name into the files database
        $productManager->insertFile($finalName, $caption, TRUE);
        
        echo "File is valid, and was successfully uploaded.\n";
    } else {
        echo "Possible file upload attack! Error code: " . $_FILES['file']['error'];
    }
}

if (isset($_GET["command"])) {
    $command = filter_input(INPUT_GET, "command", FILTER_SANITIZE_STRING);
    
    switch ($command) {
        case "delete":
            $fileIdToDelete = filter_input(INPUT_GET, "fileid", FILTER_SANITIZE_NUMBER_INT);
            $fileManager->deleteFile($fileIdToDelete);
            break;
        default:
            break;
    }
}

if (isset($_POST["save"])) {
    $title = filter_input(INPUT_POST, "title", FILTER_SANITIZE_STRING);
    $description = filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING);
    $orderLink = filter_input(INPUT_POST, "orderlink", FILTER_SANITIZE_URL);
    $redeemLink = filter_input(INPUT_POST, "redeemlink", FILTER_SANITIZE_URL);
    
    if ($session->getSelectedProductId() == -1) {
        $productManager->createProduct($title, $description, $orderLink, $redeemLink);
    } else {
        $productManager->updateCurrentProduct($title, $description, $orderLink, $redeemLink);
    }
}
    
$productManager->fetchCurrentProduct();
?>

<link rel="stylesheet" type="text/css" href="../css/progressbar.css">

<form class="w3-form" name="product-form" id="product-form" method="post" action="<?php 
    $selfURL = filter_input(INPUT_SERVER, "PHP_SELF", FILTER_SANITIZE_URL);
    echo $selfURL;
    ?>">
    <h2>Product Details</h2>
  
    <p>
        <label class="w3-label">Title</label>
        <input class="w3-input" tabindex="1" accesskey="t" name="title" type="text" maxlength="50" id="title" value="<?php echo $productManager->getTitle(); ?>" placeholder="Title">
    </p>
    
    <p>
        <label class="w3-label">Description</label>
        <textarea class="w3-input" name="description" rows="10" cols="30" tabindex="2" accesskey="d" maxlength="50" id="description" placeholder="Description"><?php echo $productManager->getDescription(); ?></textarea>
    </p>

    <p>
        <label class="w3-label">Order Link</label>
        <input class="w3-input" tabindex="3" accesskey="o" name="orderlink" type="text" maxlength="50" id="orderlink" value="<?php echo $productManager->getOrderLink(); ?>" placeholder="Order Link" />
    </p>
    
    <p>
        <label class="w3-label">Redeem Link</label>
        <input class="w3-input" tabindex="4" accesskey="r" name="redeemlink" type="text" maxlength="50" id="redeemlink" value="<?php echo $productManager->getRedeemLink(); ?>" placeholder="Redeem Link" />
    </p>

    <p>
        <input class="w3-btn" tabindex="5" accesskey="s" type="submit" name="save" value="Save" />&nbsp;<a class="w3-btn" href="index.php">Cancel</a>
    </p>
</form>

<?php
    $filesArray = $fileManager->getFiles();
    $count = count($filesArray);
?>

<div class="w3-container">
    <h2>Files</h2>
</div>

<form class="w3-form" name="upload-form" id="upload-form" enctype="multipart/form-data" method="post" action="<?php 
    $selfURL = filter_input(INPUT_SERVER, "PHP_SELF", FILTER_SANITIZE_URL);
    echo $selfURL;
    ?>">
    <h2>Add File</h2>
    
    <p>
        <label class="w3-label">Caption</label>
        <input class="w3-input" tabindex="1" accesskey="c" name="caption" type="text" maxlength="500" id="caption" placeholder="Caption">
    </p>
    
    <p>
        <label class="w3-label">Select File</label>
        <input class="w3-input" tabindex="2" accesskey="f" name="file" type="file" id="file">
    </p>
    
    <p>
        <input class="w3-btn" tabindex="3" accesskey="s" type="submit" name="submit" value="Upload" />
    </p> 
  
    <input type="hidden" value="upload-form"
    name="<?php echo ini_get("session.upload_progress.name"); ?>">
</form>

<div class="w3-container">
    <ul class="w3-ul w3-card-4">
        <?php
        for ($i = 0; $i < $count; $i++) {
        ?>
        <li class="w3-padding-16">
            <span class="w3-xlarge"><?php echo $filesArray[$i]["caption"] . ($filesArray[$i]["premium"] == TRUE ? "(premium)" : "(free)"); ?></span><br />
            <span><a class="w3-btn" href="<?php echo "editproduct.php?command=delete&fileid=" . $filesArray[$i]["fileid"] ?>">Delete</a></span>
        </li>
        <?php
        }
        ?>
    </ul>
</div>

<iframe id="hidden_iframe" name="hidden_iframe" src="about:blank"></iframe>
<script type="text/javascript" src="../js/upload.js"></script>

<!-- Upload Progress Modal -->
<div id="upload-progress-modal" class="w3-modal">
  <div class="w3-modal-content">
    <div class="w3-container">
        <div id="upload-progress-bar" class="w3-progressbar w3-green" style="width:0%">
            <div class="w3-center w3-text-white">0%</div>
        </div>
    </div>
  </div>
</div>
</body>
</html>