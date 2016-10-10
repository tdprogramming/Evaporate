<?php
require_once 'adminheader.php';
require_once '../database/ProductManager.php';
require_once '../database/Session.php';

$session = new Session();
$productManager = new ProductManager();

$productId = filter_input(INPUT_GET, "productid", FILTER_SANITIZE_NUMBER_INT);

if ($productId) {
    $session->setSelectedProductId($productId);
}

$confirmDelete = filter_input(INPUT_POST, "confirmdelete", FILTER_SANITIZE_STRING);

if ($confirmDelete != NULL) {
    $productManager->deleteCurrentProduct();
    header("Location: index.php");
    echo "Deleted. Redirecting...";
} else {
?>

<form class="w3-form" name="delete-form" id="delete-form" method="post" action="<?php 
    $selfURL = filter_input(INPUT_SERVER, "PHP_SELF", FILTER_SANITIZE_URL);
    echo $selfURL;
    ?>"> 
    <p>Deleting this product will make all issued download codes permanently invalid. Are you sure you want to delete?</p>
    <input class="w3-btn" tabindex="3" accesskey="l" type="submit" name="confirmdelete" value="Yes" />&nbsp;<a class="w3-btn" href="index.php">No</a>    
</form>

<?php
}
?>