<?php

require_once 'adminheader.php';

if (isset($_GET["productid"])) {
    $session->setSelectedProductId($_GET["productid"]);
}

if (isset($_POST['cmdgenerate'])) {
    $codeManager = new CodeManager();
    $codeManager->generateCodes(filter_input(INPUT_POST, 'numcodes', FILTER_SANITIZE_NUMBER_INT));
    echo "Done";
} else {
?>

<form class="w3-form" name="product-form" id="product-form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <h2>Create Code Batch</h2>
  
    <p>
        <label class="w3-label">Name of this batch</label>
        <input class="w3-input" tabindex="1" accesskey="n" name="numcodes" type="number" maxlength="50" id="batchname" /> 
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
</body>
</html>


<?php
}

?>