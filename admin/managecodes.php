<?php

require_once 'adminheader.php';

$productId = filter_input(INPUT_GET, "productid", FILTER_SANITIZE_NUMBER_INT);

if ($productId != NULL) {
    $session->setSelectedProductId($productId);
}

$codeManager = new CodeManager();

$cmdGenerate = filter_input(INPUT_POST, "cmdgenerate", FILTER_SANITIZE_NUMBER_INT);

if ($cmdGenerate) {
    $codeManager->generateCodes(filter_input(INPUT_POST, 'numcodes', FILTER_SANITIZE_NUMBER_INT), $_POST['batchname']);
}

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
</body>
</html>