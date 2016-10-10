<?php
    require_once "../database/ProductManager.php";
    require_once "../database/Session.php";

    $session = new Session();
        
    if (isset($_GET["productid"])) {
        $session->setSelectedProductId($_GET["productid"]);
    }
    
    if (isset($_POST['submit'])) {
        // TODO: validate upload file name

        $productManager = new ProductManager();
        
        $selectedProductId = $session->getSelectedProductId();
        $premiumFile = FALSE;
        
        if (isset($_POST['premium']) && $_POST['premium'] == 'Yes')
        {
            $uploaddir = '../downloads/premium/product' . $selectedProductId . "/";
            $premiumFile = TRUE;
        }
        else
        {
            $uploaddir = '../downloads/free/product' . $selectedProductId . "/";
        }
        
        $finalName = basename($_FILES['file']['name']);
        $uploadfile = $uploaddir . $finalName;
        
        // Note - need appropriate file size limits here
        if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {
            echo "File is valid, and was successfully uploaded.\n";
        } else {
            echo "Possible file upload attack! Error code: " . $_FILES['file']['error'];
        }
        
        $caption = $_POST["caption"];
        // Insert the file name into the files database
        $productManager->insertFile($finalName, $caption, $premiumFile);
    }
    
    require_once 'adminheader.php';
?>

<form name="upload-form" id="upload-form" enctype="multipart/form-data" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>"> 
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
        <label title="Premium File">Premium File:
            <input tabindex="3" accesskey="p" name="premium" type="checkbox" id="premium" value="Yes" /> 
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
</form>