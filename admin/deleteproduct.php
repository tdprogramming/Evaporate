<?php
require_once 'adminheader.php';
require_once '../database/ProductManager.php';
require_once '../database/Session.php';

$session = new Session();
$productManager = new ProductManager();

if (isset($_GET["productid"])) {
    $session->setSelectedProductId($_GET["productid"]);
}

if (isset($_POST['confirmdelete'])) {
    $productManager->deleteCurrentProduct();
    header("Location: index.php");
    echo "Deleted. Redirecting...";
} else {
?>

<form class="w3-form" name="delete-form" id="delete-form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>"> 
    <p>Deleting this product will make all issued download codes permanently invalid. Are you sure you want to delete?</p>
    <input class="w3-btn" tabindex="3" accesskey="l" type="submit" name="confirmdelete" value="Yes" />&nbsp;<a class="w3-btn" href="index.php">No</a>    
</form>

<?php
}
?>