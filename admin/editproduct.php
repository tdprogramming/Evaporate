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
        // Insert the file name into the files database
        $productManager->insertFile($finalName, $finalName, TRUE);
        
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
    $codeManager->generateCodes(filter_input(INPUT_POST, 'numcodes', FILTER_SANITIZE_NUMBER_INT));
}
    
$productManager->fetchCurrentProduct();
?>

<link rel="stylesheet" type="text/css" href="../css/progressbar.css">

<div class="w3-container">
    <label id="pendingChangesLabel" class="w3-label">All Changes Saved</label>
</div>

<div class="w3-container">
    <h2>Image</h2>
    
    <?php
    $imagePath = "../images/product" . $session->getSelectedProductId() . "/image0.png";
                    
    if (!file_exists($imagePath)) {
        $imagePath = "../images/productdefault.png";
    }
    ?>
    
    <p>
        <img src="<?php echo $imagePath; ?>" class="w3-left" style="width:200px;height:200px" />
    </p>
</div>
<div class="w3-container">
    <form class="w3-form" name="image-form" id="image-form" enctype="multipart/form-data" method="post" action="<?php 
        $selfURL = filter_input(INPUT_SERVER, "PHP_SELF", FILTER_SANITIZE_URL);
        echo $selfURL;
        ?>">
        <p>
            <label class="w3-label">Change Image</label>
        </p>
        
        <p>
            <input type="file" class="w3-btn" style="width:400px" name="imageToUpload" id="imageToUpload" onchange="imageSelected();" />
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

        <input type="button" class="w3-btn" style="width:400px" onclick="uploadImage()" value="Upload"/>
    </form>
</div>

<form class="w3-form" name="product-form" id="product-form" method="post" action="<?php 
    $selfURL = filter_input(INPUT_SERVER, "PHP_SELF", FILTER_SANITIZE_URL);
    echo $selfURL;
    ?>">
    <h2>Product Details</h2>

    <p>
        <label class="w3-label">Title</label>
        <input class="w3-input" onkeydown="onDetailsChange()" tabindex="1" accesskey="t" name="title" type="text" maxlength="50" id="title" value="<?php echo $productManager->getTitle(); ?>" placeholder="Title">
    </p>
    
    <p>
        <label class="w3-label">Description</label>
        <textarea class="w3-input" onkeydown="onDetailsChange()" name="description" rows="10" cols="30" tabindex="2" accesskey="d" maxlength="500" id="description" placeholder="Description"><?php echo $productManager->getDescription(); ?></textarea>
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
    
    if ($count == 0) {
        $fileCaptionInputDetails = "[]";
    } else {
        $fileCaptionInputDetails = "[";
    }
?>

<div class="w3-container">
    <h2>Files</h2>


    <form class="w3-form" name="upload-form" id="upload-form" enctype="multipart/form-data" method="post" action="<?php 
        $selfURL = filter_input(INPUT_SERVER, "PHP_SELF", FILTER_SANITIZE_URL);
        echo $selfURL;
        ?>">
        <p>
            <label class="w3-label">Add File</label>
        </p>
        
        <p>
            <input type="file" class="w3-btn" style="width:400px" name="fileToUpload" id="fileToUpload" onchange="fileSelected();" caption="Add File" />
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

        <input class="w3-btn" style="width:400px" type="button" onclick="uploadFile()" value="Upload"/>
    </form>
</div>

<div class="w3-container">
    <table class="w3-table w3-striped">
        <?php
        for ($i = 0; $i < $count; $i++) {
            $fileCaptionInputDetails .= "{ fileId: " . $filesArray[$i]["fileid"] . ", inputId: \"fileCaptionInput" . $i . "\", caption: \"" . $filesArray[$i]["caption"] . "\"}";
        ?>
        
        
        <tr>
        <td width="20%">
            <input class="w3-input" id="fileCaptionInput<?php echo $i; ?>" onkeydown="onDetailsChange()" type="text" value="<?php echo $filesArray[$i]["caption"]; ?>" />
        </td>
        <td width="20%">
            <label class="w3-label"><?php echo $filesArray[$i]["filename"]; ?></label>
        </td>
        <td width="20%">
            <label class="w3-label"><?php echo ($filesArray[$i]["premium"] == TRUE ? "Premium" : "Free"); ?></label>
        </td>
        <td width="20%">
            <?php
                if ($filesArray[$i]["premium"] == TRUE)
                {
                    ?>
                        <a class="w3-btn" style="width:180px" href="<?php echo "editproduct.php?command=makefree&fileid=" . $filesArray[$i]["fileid"] ?>">Make Free</a>
                    <?php
                }
                else
                {
                    ?>
                        <a class="w3-btn" style="width:180px" href="<?php echo "editproduct.php?command=makepremium&fileid=" . $filesArray[$i]["fileid"] ?>">Make Premium</a>
                    <?php                
                }
            ?>
        </td>
        <td width="20%">
            <a class="w3-btn" style="width:180px" href="<?php echo "editproduct.php?command=delete&fileid=" . $filesArray[$i]["fileid"] ?>">Delete</a>
        </td>
    </tr>
        

        <?php
            if ($i < $count - 1) {
                $fileCaptionInputDetails .= ", ";
            } else {
                $fileCaptionInputDetails .= "]";
            }
        }
        ?>
    </table>
</div>

<div class="w3-container">
    <h2>Codes</h2>
<?php
$codeBatchesArray = $codeManager->fetchAllCodeBatches();
$count = count($codeBatchesArray);

if ($count == 0) {
    $batchCaptionInputDetails = "[]";
    ?>
    <p>You have no codes set up yet.</p>
    <?php
} else {
    $batchCaptionInputDetails = "[";
?>

<table class="w3-table w3-striped">
<?php
for ($i = 0; $i < $count; $i++) {
    $batchCaptionInputDetails .= "{ batchId: " . $codeBatchesArray[$i]["batchid"] . ", inputId: \"batchCaptionInput" . $i . "\", caption: \"" . $codeBatchesArray[$i]["batchname"] . "\"}";
    ?><tr>
        <td width="50%">
            <input class="w3-input" id="batchCaptionInput<?php echo $i; ?>" onkeydown="onDetailsChange()" type="text" value="<?php echo $codeBatchesArray[$i]["batchname"]; ?>" />
        </td>
        <td width="50%">
            <?php echo "<a target=\"_blank\" class=\"w3-btn\" style=\"width:180px\" href=\"createcodespdf.php?batchid=" . $codeBatchesArray[$i]["batchid"] ."\">Get PDF Of Codes</a>"; ?>
        </td>
    </tr>
<?php    
    if ($i < $count - 1) {
        $batchCaptionInputDetails .= ", ";
    } else {
        $batchCaptionInputDetails .= "]";
    }
}}
?>
</table>
    
<form class="w3-form" name="product-form" id="product-form" method="post" action="<?php 
    $selfURL = filter_input(INPUT_SERVER, "PHP_SELF", FILTER_SANITIZE_URL);
    echo $selfURL;
    ?>">
    <h2>Create Code Batch</h2>
  
    <p>
        <label class="w3-label">Number of codes</label>
        <input class="w3-input" tabindex="2" accesskey="c" name="numcodes" type="number" maxlength="50" id="numcodes" value="100" /> 
    </p>

    <p>
        <label title="Submit"> 
        <input class="w3-btn" style="width:180px" tabindex="3" accesskey="s" type="submit" name="cmdgenerate" value="Submit" /> 
    </p>
</form>
</div>

<script type="text/javascript" src="../js/upload.js"></script>
<script>
    setupCaptionInputs(<?php echo $fileCaptionInputDetails . ", " . $batchCaptionInputDetails; ?>);
</script>

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