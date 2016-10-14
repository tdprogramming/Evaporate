<?php

require_once "Connector.php";
require_once "Session.php";

class ProductManager {
    private $connection;
    private $session;
    
    // Storage for the current selected product
    private $title;
    private $description;
    private $orderlink;
    private $redeemlink;
    
    public function __construct() {
        $connector = new Connector();
        $this->connection = $connector->getConnection();
        $this->session = new Session();
    }
    
    public function createProduct($newTitle, $newDescription, $newOrderLink, $newRedeemLink) {
        $preparedQuery = $this->connection->prepare("INSERT INTO products(title,description,orderlink,redeemlink) VALUES (?,?,?,?);");
        $preparedQuery->bind_param('ssss', $newTitle, $newDescription, $newOrderLink, $newRedeemLink);
        $preparedQuery->execute();
        $selectedProductId = $preparedQuery->insert_id;
        $this->session->setSelectedProductId($selectedProductId);
        
        // Create folders for product files - TODO: Get advice on most secure mode setting to use
        if (!is_dir("../downloads/product" . $selectedProductId)) {
            mkdir("../downloads/product" . $selectedProductId, 0777);
        }
        
        if (!is_dir("../images/product" . $selectedProductId)) {
            mkdir("../images/product" . $selectedProductId, 0777);
        }
    }
    
    public function insertFile($fileName, $caption, $premium) {
        $selectedProductId = $this->session->getSelectedProductId();
        
        $preparedQuery = $this->connection->prepare("INSERT INTO files(productid, caption, filename, premium) VALUES (?, ?, ?, ?);");
        $preparedQuery->bind_param('sssi', $selectedProductId, $caption, $fileName, $premium);
        $preparedQuery->execute();
    }
    
    public function fetchCurrentProduct() {
        $selectedProductId = $this->session->getSelectedProductId();
        $preparedQuery = $this->connection->prepare("SELECT title,description,orderlink,redeemlink FROM products WHERE productid=?;");
        $preparedQuery->bind_param('d', $selectedProductId);
        $preparedQuery->bind_result($this->title, $this->description, $this->orderlink, $this->redeemlink);
        $preparedQuery->execute();
        $preparedQuery->fetch();
    }
    
    public function updateCurrentProduct($newTitle, $newDescription, $newOrderLink) {
        $selectedProductId = $this->session->getSelectedProductId();
        $preparedQuery = $this->connection->prepare("UPDATE products SET title=?,description=?,orderlink=? WHERE productid=?;");
        $preparedQuery->bind_param('sssd', $newTitle, $newDescription, $newOrderLink, $selectedProductId);
        $preparedQuery->execute();
        $this->fetchCurrentProduct();
    }
    
    public function deleteCurrentProduct() {
        $selectedProductId = $this->session->getSelectedProductId();
        $preparedQuery = $this->connection->prepare("DELETE FROM products WHERE productid=? LIMIT 1;");
        $preparedQuery->bind_param('d', $selectedProductId);
        $preparedQuery->execute();
    }
    
    public function fetchAllProducts() {
        $result = array();
        
        $preparedQuery = $this->connection->prepare("SELECT productid,title,description FROM products;");
        
        if (!$preparedQuery) {
            die("Error fetching products");
        }
        
        $preparedQuery->bind_result($productid, $title, $description);
        $preparedQuery->execute();
        
        while ($preparedQuery->fetch()) {
            array_push($result, array("productid" => $productid, "title" => $title, "description" => $description));
        }
        
        return $result;
    }
    
    public function getTitle() {
        return $this->title;
    }
    
    public function getDescription() {
        return $this->description;
    }
   
    public function getOrderLink() {
        return $this->orderlink;
    }
    
    public function getRedeemLink() {
        return $this->redeemlink;
    }
}