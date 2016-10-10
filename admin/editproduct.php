<?php

require_once '../database/ProductManager.php';
require_once '../database/Session.php';

$session = new Session();
$productManager = new ProductManager();

require_once 'adminheader.php';

$productId = filter_input(INPUT_GET, "productid", FILTER_SANITIZE_NUMBER_INT);

if (isset($productId)) {
    $session->setSelectedProductId($productId);
} else {
    $session->setSelectedProductId(-1);
}

$submit = filter_input(INPUT_POST, "submit", FILTER_SANITIZE_NUMBER_INT);

if ($submit != NULL) {
    $title = filter_input(INPUT_POST, "title", FILTER_SANITIZE_STRING);
    $description = filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING);
    $orderLink = filter_input(INPUT_POST, "orderlink", FILTER_SANITIZE_URL);
    $redeemLink = filter_input(INPUT_POST, "redeemlink", FILTER_SANITIZE_URL);
    
    if ($session->getSelectedProductId() == -1) {
        $productManager->createProduct($title, $description, $orderLink, $redeemLink);
    } else {
        $productManager->updateCurrentProduct($title, $description, $orderLink, $redeemLink);
    }
    
    header("Location: index.php");
} else {
    $productManager->fetchCurrentProduct();
?>

<form class="w3-form" name="product-form" id="product-form" method="post" action="<?php 
    $selfURL = filter_input(INPUT_SERVER, "PHP_SELF", FILTER_SANITIZE_URL);
    echo $selfURL;
    ?>">
    <h2>Product Details</h2>
  
    <p>
        <label class="w3-label">Title</label>
        <input class="w3-input" tabindex="1" accesskey="t" name="title" type="text" maxlength="50" id="title" value="<?php echo $productManager->getTitle(); ?>" placeholder="Title">
    </p>
    
    <p>
        <label class="w3-label">Description</label>
        <textarea class="w3-input" name="description" rows="10" cols="30" tabindex="2" accesskey="d" maxlength="50" id="description" placeholder="Description"><?php echo $productManager->getDescription(); ?></textarea>
    </p>

    <p>
        <label class="w3-label">Order Link</label>
        <input class="w3-input" tabindex="3" accesskey="o" name="orderlink" type="text" maxlength="50" id="orderlink" value="<?php echo $productManager->getOrderLink(); ?>" placeholder="Order Link" />
    </p>
    
    <p>
        <label class="w3-label">Redeem Link</label>
        <input class="w3-input" tabindex="4" accesskey="r" name="redeemlink" type="text" maxlength="50" id="redeemlink" value="<?php echo $productManager->getRedeemLink(); ?>" placeholder="Redeem Link" />
    </p>

    <p>
        <input class="w3-btn" tabindex="5" accesskey="s" type="submit" name="submit" value="Save" />&nbsp;<a class="w3-btn" href="index.php">Cancel</a>
    </p>
</form>
</body>
</html>
<?php }