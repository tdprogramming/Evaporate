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
    <div class="w3-container">
        <p>You have no products set up yet. Use "Add Product" to add one.</p>
    </div>
    <?php
} else {
?>

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <div class="w3-container">
        <h2>Products&nbsp;<a href="index.php?createproduct=1"><i class="material-icons">add_box</i></a></h2>

    </div>
<table class="w3-table-all w3-hoverable">
<?php
for ($i = 0; $i < $count; $i++) {
    ?><tr onclick="window.document.location='<?php echo "editproduct.php?productid=" . $productsArray[$i]["productid"]; ?>';">
        <td>
            <?php echo $productsArray[$i]["title"]; ?>
        </td>
        <td style="text-align: right;">
            <?php echo "<a href=\"deleteproduct.php?productid=" . $productsArray[$i]["productid"] ."\"><i class=\"material-icons\">delete</i></a>"; ?>
        </td>
    </tr>
<?php    
}
?>
</table>
<?php } ?>