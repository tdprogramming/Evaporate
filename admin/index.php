<?php

require_once "adminheader.php";
require_once "../database/Session.php";
require_once "../database/ProductManager.php";

$session = new Session();

if (!$session->isLoggedIn()) {
    header("Location: login.php");
    die("Redirecting you to the login page...");
}

$productManager = new ProductManager();
$productsArray = $productManager->fetchAllProducts();
$count = count($productsArray);

if ($count == 0) {
    ?>
    <p>You have no products set up yet. Use "Add Product" to add one.
    <?php
} else {
?>

<table class="w3-table w3-striped">
  <tr>
    <th>Product ID</th>
    <th>Product Name</th> 
    <th>Actions</th>
  </tr>
<?php
for ($i = 0; $i < $count; $i++) {
    ?><tr>
        <td>
            <?php echo $productsArray[$i]["productid"]; ?>
        </td>
        <td>
            <?php echo $productsArray[$i]["title"]; ?>
        </td>
        <td>
            <?php echo "<a class=\"w3-btn\" href=\"editproduct.php?productid=" . $productsArray[$i]["productid"] ."\">Edit Details</a>"; ?>
            &nbsp;
            <?php echo "<a class=\"w3-btn\" href=\"createcodes.php?productid=" . $productsArray[$i]["productid"] ."\">Create Codes</a>"; ?>
            &nbsp;
            <?php echo "<a class=\"w3-btn\" href=\"printcodes.php?productid=" . $productsArray[$i]["productid"] ."\">Print Codes</a>"; ?>
            &nbsp;
            <?php echo "<a class=\"w3-btn\" href=\"uploadproductimage.php?productid=" . $productsArray[$i]["productid"] ."\">Upload Image</a>"; ?>
            &nbsp;
            <?php echo "<a class=\"w3-btn\" href=\"uploadproductfile.php?productid=" . $productsArray[$i]["productid"] ."\">Upload File</a>"; ?>
            &nbsp;
            <?php echo "<a class=\"w3-btn\" href=\"deleteproduct.php?productid=" . $productsArray[$i]["productid"] ."\">Delete Product</a>"; ?>
        </td>
    </tr>
<?php    
}
?>
</table>
<?php } ?>

<div class="w3-container">
    <p>
        <a class="w3-btn" href="editproduct.php">Add Product</a>
    </p>
</div>