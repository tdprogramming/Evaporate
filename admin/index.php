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

if (isset($_GET["createproduct"])) {
    $productManager->createProduct("Untitled Product", "Untitled Product", "", "");
}

$productsArray = $productManager->fetchAllProducts();
$count = count($productsArray);

if ($count == 0) {
    ?>
    <p>You have no products set up yet. Use "Add Product" to add one.
    <?php
} else {
?>

<table class="w3-table w3-striped">
<?php
for ($i = 0; $i < $count; $i++) {
    ?><tr onclick="window.document.location='<?php echo "editproduct.php?productid=" . $productsArray[$i]["productid"]; ?>';">
        <td>
            <?php echo $productsArray[$i]["title"]; ?>
        </td>
        <td>
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
        <a class="w3-btn" href="index.php?createproduct=1">Add Product</a>
    </p>
</div>