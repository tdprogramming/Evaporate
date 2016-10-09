<?php

require_once 'adminheader.php';

if (isset($_GET["productid"])) {
    $session->setSelectedProductId($_GET["productid"]);
}

$codeManager = new CodeManager();

if (isset($_GET["batchid"])) {
    $codeManager->printCodes($_GET["batchid"]);
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
            <?php echo "<a class=\"w3-btn\" href=\"printcodes.php?batchid=" . $codeBatchesArray[$i]["batchid"] ."\">Print</a>"; ?>
        </td>
    </tr>
<?php    
}}
?>
</table>
