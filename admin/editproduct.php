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

$selectedProductId = $session->getSelectedProductId();

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_FILES["imageToUpload"])) {
    $uploaddir = '../images/product' . $selectedProductId . "/";
    $uploadfile = $uploaddir . "image0.png";

    if (move_uploaded_file($_FILES['imageToUpload']['tmp_name'], $uploadfile)) {
        echo "File is valid, and was successfully uploaded.\n";
    } else {
        echo "Possible file upload attack! Error code: " . $_FILES['userfile']['error'];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_FILES["fileToUpload"])) {
    echo "Got file";
    // TODO: validate upload file name
    $uploaddir = '../downloads/product' . $selectedProductId . "/";
    $finalName = basename($_FILES['fileToUpload']['name']);
    $uploadfile = $uploaddir . $finalName;
        
    // Note - need appropriate file size limits here
    if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $uploadfile)) {
        $caption = filter_input(INPUT_POST, "caption", FILTER_SANITIZE_STRING);
        // Insert the file name into the files database
        $productManager->insertFile($finalName, $caption, TRUE);
        
        echo "File is valid, and was successfully uploaded.\n";
    } else {
        echo "Possible file upload attack! Error code: " . $_FILES['fileToUpload']['error'];
    }
}

if (isset($_GET["command"])) {
    $command = filter_input(INPUT_GET, "command", FILTER_SANITIZE_STRING);
    $fileIdToChange = filter_input(INPUT_GET, "fileid", FILTER_SANITIZE_NUMBER_INT);
    
    switch ($command) {
        case "delete":
            $fileManager->deleteFile($fileIdToChange);
            break;
        case "makefree":
            $fileManager->changePremiumStatus($fileIdToChange, FALSE);
            break;
        case "makepremium":
            $fileManager->changePremiumStatus($fileIdToChange, TRUE);
            break;        
        default:
            break;
    }
}

$codeManager = new CodeManager();

if (isset($_POST["cmdgenerate"])) {
    $codeManager->generateCodes(filter_input(INPUT_POST, 'numcodes', FILTER_SANITIZE_NUMBER_INT), $_POST['batchname']);
}
    
$productManager->fetchCurrentProduct();
?>

<link rel="stylesheet" type="text/css" href="../css/progressbar.css">

<div class="w3-container">
    <h2>Image</h2>

    <?php
    $imagePath = "../images/product" . $session->getSelectedProductId() . "/image0.png";
                    
    if (!file_exists($imagePath)) {
        $imagePath = "../images/productdefault.png";
    }
    ?>
    
    <img src="<?php echo $imagePath; ?>" class="w3-left" style="width:50px;height:50px">
    
    <form class="w3-form" name="image-form" id="image-form" enctype="multipart/form-data" method="post" action="<?php 
        $selfURL = filter_input(INPUT_SERVER, "PHP_SELF", FILTER_SANITIZE_URL);
        echo $selfURL;
        ?>">
        <p>
            <label class="w3-label">Choose File</label>
            <input type="file" name="imageToUpload" id="imageToUpload" onchange="imageSelected();" />
        </p>

        <p>
            <label class="w3-label" id="imageName"></label>
        </p>
        <p>
            <label class="w3-label" id="imageSize"></label>
        </p>
        <p>
            <label class="w3-label" id="imageType"></label>
        </p>

        <input type="button" onclick="uploadImage()" value="Upload"/>
    </form>
</div>

<form class="w3-form" name="product-form" id="product-form" method="post" action="<?php 
    $selfURL = filter_input(INPUT_SERVER, "PHP_SELF", FILTER_SANITIZE_URL);
    echo $selfURL;
    ?>">
    <h2>Product Details</h2>

    <p>
        <label id="pendingChangesLabel" class="w3-label">All Changes Saved</label>
    </p>
    
    <p>
        <label class="w3-label">Title</label>
        <input class="w3-input" onkeydown="onDetailsChange()" tabindex="1" accesskey="t" name="title" type="text" maxlength="50" id="title" value="<?php echo $productManager->getTitle(); ?>" placeholder="Title">
    </p>
    
    <p>
        <label class="w3-label">Description</label>
        <textarea class="w3-input" onkeydown="onDetailsChange()" name="description" rows="10" cols="30" tabindex="2" accesskey="d" maxlength="50" id="description" placeholder="Description"><?php echo $productManager->getDescription(); ?></textarea>
    </p>

    <p>
        <label class="w3-label">Order Link</label>
        <input class="w3-input" onkeydown="onDetailsChange()" tabindex="3" accesskey="o" name="orderlink" type="text" maxlength="50" id="orderlink" value="<?php echo $productManager->getOrderLink(); ?>" placeholder="Order Link" />
    </p>
    
    <p>
        <label class="w3-label">Redeem Link</label>
        <input class="w3-input" onkeydown="onDetailsChange()" tabindex="4" accesskey="r" name="redeemlink" type="text" maxlength="50" id="redeemlink" value="<?php echo $productManager->getRedeemLink(); ?>" placeholder="Redeem Link" />
    </p>
</form>

<?php
    $filesArray = $fileManager->getFiles();
    $count = count($filesArray);
?>

<div class="w3-container">
    <h2>Files</h2>


    <form class="w3-form" name="upload-form" id="upload-form" enctype="multipart/form-data" method="post" action="<?php 
        $selfURL = filter_input(INPUT_SERVER, "PHP_SELF", FILTER_SANITIZE_URL);
        echo $selfURL;
        ?>">
        <p>
            <label class="w3-label">Choose File</label>
            <input type="file" name="fileToUpload" id="fileToUpload" onchange="fileSelected();" />
        </p>

        <p>
            <label class="w3-label">Caption</label>
            <input id="caption" class="w3-input" type="text"/>
        </p>    

        <p>
            <label class="w3-label" id="fileName"></label>
        </p>
        <p>
            <label class="w3-label" id="fileSize"></label>
        </p>
        <p>
            <label class="w3-label" id="fileType"></label>
        </p>

        <input type="button" onclick="uploadFile()" value="Upload"/>
    </form>
</div>

<!--
<div class="w3-border-bottom w3-border-red">
    <p>Here is a Border</p>
    
</div>
-->

<div class="w3-container">
    <ul class="w3-ul w3-card-4">
        <?php
        for ($i = 0; $i < $count; $i++) {
        ?>
        <li class="w3-padding-16">
            <span class="w3-xlarge"><?php echo $filesArray[$i]["caption"] . ($filesArray[$i]["premium"] == TRUE ? "(premium)" : "(free)"); ?></span><br />
            <span>
                <?php
                    if ($filesArray[$i]["premium"] == TRUE)
                    {
                        ?>
                            <a class="w3-btn" href="<?php echo "editproduct.php?command=makefree&fileid=" . $filesArray[$i]["fileid"] ?>">Make Free</a>
                        <?php
                    }
                    else
                    {
                        ?>
                            <a class="w3-btn" href="<?php echo "editproduct.php?command=makepremium&fileid=" . $filesArray[$i]["fileid"] ?>">Make Premium</a>
                        <?php                
                    }
                ?>
                <a class="w3-btn" href="<?php echo "editproduct.php?command=delete&fileid=" . $filesArray[$i]["fileid"] ?>">Delete</a>
            </span>
        </li>
        <?php
        }
        ?>
    </ul>
</div>

<?php
$codeBatchesArray = $codeManager->fetchAllCodeBatches();
$count = count($codeBatchesArray);

if ($count == 0) {
    ?>
    <p>You have no codes set up yet.</p>
    <?php
} else {
?>

<table class="w3-table w3-striped">
  <tr>
    <th>Batch ID</th>
    <th>Batch Name</th> 
    <th>Print</th>
  </tr>
<?php
for ($i = 0; $i < $count; $i++) {
    ?><tr>
        <td>
            <?php echo $codeBatchesArray[$i]["batchid"]; ?>
        </td>
        <td>
            <?php echo $codeBatchesArray[$i]["batchname"]; ?>
        </td>
        <td>
            <?php echo "<a target=\"_blank\" class=\"w3-btn\" href=\"createcodespdf.php?batchid=" . $codeBatchesArray[$i]["batchid"] ."\">Get PDF Of Codes</a>"; ?>
        </td>
    </tr>
<?php    
}}
?>
</table>
    
<form class="w3-form" name="product-form" id="product-form" method="post" action="<?php 
    $selfURL = filter_input(INPUT_SERVER, "PHP_SELF", FILTER_SANITIZE_URL);
    echo $selfURL;
    ?>">
    <h2>Create Code Batch</h2>
  
    <p>
        <label class="w3-label">Name of this batch</label>
        <input class="w3-input" tabindex="1" accesskey="n" name="batchname" type="text" maxlength="50" id="batchname" /> 
    </p>
    
    <p>
        <label class="w3-label">Number of codes</label>
        <input class="w3-input" tabindex="2" accesskey="c" name="numcodes" type="number" maxlength="50" id="numcodes" /> 
    </p>

    <p>
        <label title="Submit"> 
        <input class="w3-btn" tabindex="3" accesskey="s" type="submit" name="cmdgenerate" value="Submit" /> 
    </p>
</form>

<script type="text/javascript" src="../js/upload.js"></script>

<!-- Upload Progress Modal -->
<div id="upload-progress-modal" class="w3-modal">
  <div class="w3-modal-content">
    <div class="w3-container">
        <div id="upload-progress-bar" class="w3-progressbar w3-green" style="width:0%">
            <div id="progressNumber" class="w3-center w3-text-white">0%</div>
        </div>
    </div>
  </div>
</div>
</body>
</html>