<?php

require_once 'database/CodeManager.php';
require_once 'database/ProductManager.php';
require_once 'database/FileManager.php';

$productManager = new ProductManager();
$fileManager = new FileManager();
$productsArray = $productManager->fetchAllProducts();
$count = count($productsArray);

if ($count == 0) {
    ?>
    <p>Sorry, there are no products available at the moment.
    <?php
} else {
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Products</title>
    </head>
    <link rel="stylesheet" href="css/w3.css">
    <body>
        <div class="w3-container w3-blue">
            <h2>Products</h2>
        </div>

        <div class="w3-container">
            <ul class="w3-ul w3-card-4">
                <?php
                for ($i = 0; $i < $count; $i++) {
                ?>
                <li class="w3-padding-16">
                    <?php
                    $productId = $productsArray[$i]["productid"];
                    $hasFreeDownloads = $fileManager->hasFreeDownloads($productId);
                    $hasPremiumDownloads = $fileManager->hasPremiumDownloads($productId);
                    
                    $imagePath = "images/product" . $productId . "/image0.png";
                    
                    if (!file_exists($imagePath)) {
                        $imagePath = "images/productdefault.png";
                    }
                    ?>
                    <img src="<?php echo $imagePath; ?>" class="w3-left" style="width:50px;height:50px">
                    <span class="w3-xlarge"><?php echo $productsArray[$i]["title"] ?></span><br />
                    <span><?php echo $productsArray[$i]["description"] ?></span><br />
                    <span>
                        <?php 
                        if ($hasFreeDownloads) {
                            ?>
                            <a class="w3-btn" href=<?php echo "\"showfreedownloads.php?productid=" . $productId . "\"" ?>>Free Downloads</a>&nbsp;
                            <?php
                        }
                        
                        if ($hasPremiumDownloads) {
                            ?>
                            <a class="w3-btn" href=<?php echo "\"redeemcode.php?productid=" . $productId . "\"" ?>>Redeem Code</a>
                            <?php
                        }
                        ?>
                    </span>
                </li>
                <?php
                }
                ?>
            </ul>
        </div>
<?php } ?>
    </body>
</html>