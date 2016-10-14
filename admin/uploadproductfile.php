<?php
    require_once "../database/ProductManager.php";
    require_once "../database/Session.php";

    $session = new Session();
    
    $productId = filter_input(INPUT_GET, "productid", FILTER_SANITIZE_NUMBER_INT);
        
    if ($productId != NULL) {
        $session->setSelectedProductId($productId);
    }
    
    if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_FILES["file"])) {
        // TODO: validate upload file name

        $productManager = new ProductManager();
        
        $selectedProductId = $session->getSelectedProductId();
        $premiumFile = FALSE;
        
        $premium = filter_input(INPUT_POST, "premium", FILTER_SANITIZE_STRING);
        
        $uploaddir = '../downloads/product' . $selectedProductId . "/";
        
        $finalName = basename($_FILES['file']['name']);
        $uploadfile = $uploaddir . $finalName;
        
        // Note - need appropriate file size limits here
        if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {
            echo "File is valid, and was successfully uploaded.\n";
        } else {
            echo "Possible file upload attack! Error code: " . $_FILES['file']['error'];
        }
        
        $caption = filter_input(INPUT_POST, "caption", FILTER_SANITIZE_STRING);

        // Insert the file name into the files database
        $productManager->insertFile($finalName, $caption, TRUE);
    }
    
    require_once 'adminheader.php';
?>

<link rel="stylesheet" type="text/css" href="../css/progressbar.css">

<div id="bar_blank">
   <div id="bar_color"></div>
</div>
<div id="status"></div>

<form name="upload-form" id="upload-form" enctype="multipart/form-data" method="post" target="hidden_iframe"
    action="<?php 
    $selfURL = filter_input(INPUT_SERVER, "PHP_SELF", FILTER_SANITIZE_URL);
    echo $selfURL;
    ?>"> 
  <fieldset> 
    <legend>Select file to add to this product:</legend>
    <dl> 
        <dt> 
            <label title="Caption">Caption:
            <input tabindex="1" accesskey="c" name="caption" type="text" maxlength="500" id="caption" /> 
            </label> 
        </dt> 
    </dl> 
    <dl> 
      <dt> 
        <label title="Select File">Select File:
            <input tabindex="2" accesskey="f" name="file" type="file" id="file" /> 
        </label> 
      </dt> 
    </dl> 
    <dl> 
      <dt> 
        <label title="Submit"> 
            <input tabindex="4" accesskey="s" type="submit" name="submit" value="Upload" /> 
        </label> 
      </dt> 
    </dl> 
  </fieldset> 
    <input type="hidden" value="upload-form"
    name="<?php echo ini_get("session.upload_progress.name"); ?>">
</form>

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