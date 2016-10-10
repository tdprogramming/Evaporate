<?php
    require_once "../database/ProductManager.php";
    require_once "../database/Session.php";

    $session = new Session();
    
    $productId = filter_input(INPUT_GET, "productid", FILTER_SANITIZE_NUMBER_INT);
    
    if ($productId != NULL) {
        $session->setSelectedProductId($productId);
    }
    
    $submit = filter_input(INPUT_POST, "submit", FILTER_SANITIZE_NUMBER_INT);
    
    if ($submit != NULL) {
        // TODO: validate upload file name

        $selectedProductId = $session->getSelectedProductId();
        $uploaddir = '../images/product' . $selectedProductId . "/";
        $uploadfile = $uploaddir . "image0.png";

        if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {
            echo "File is valid, and was successfully uploaded.\n";
        } else {
            echo "Possible file upload attack! Error code: " . $_FILES['userfile']['error'];
        }
    }
    require_once 'adminheader.php';
    
?>

<form name="upload-form" id="upload-form" method="post" enctype="multipart/form-data" action="<?php 
    $selfURL = filter_input(INPUT_SERVER, "PHP_SELF", FILTER_SANITIZE_URL);
    echo $selfURL;
    ?>"> 
  <fieldset> 
    <legend>Select image to add to this product (must be PNG)</legend> 
    <dl> 
      <dt> 
        <label title="Select File">Select File:
            <input tabindex="1" accesskey="f" name="file" type="file" id="file" /> 
        </label> 
      </dt> 
    </dl> 
    <dl> 
      <dt> 
        <label title="Submit"> 
            <input tabindex="2" accesskey="s" type="submit" name="submit" value="Upload" /> 
        </label> 
      </dt> 
    </dl> 
  </fieldset> 
</form>

<p>Please note that Evaporate does not currently support an upload progress bar. One will be added soon! For large files, please wait patiently for the upload to complete.</p>