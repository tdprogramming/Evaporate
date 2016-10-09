<?php
    require_once "../database/ProductManager.php";
    require_once "../database/Session.php";

    $session = new Session();
        
    if (isset($_GET["productid"])) {
        $session->setSelectedProductId($_GET["productid"]);
    }
    
    if (isset($_POST['submit'])) {
        // TODO: validate upload file name

        $selectedProductId = $session->getSelectedProductId();
        $uploaddir = '../images/product' . $selectedProductId . "/";
        $uploadfile = $uploaddir . "image0.png";

        if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {
            echo "File is valid, and was successfully uploaded.\n";
        } else {
            echo "Possible file upload attack!\n";
        }
    }
    require_once 'adminheader.php';
    
?>

<form name="upload-form" id="upload-form" method="post" enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF']; ?>"> 
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