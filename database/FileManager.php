<?php

require_once "Connector.php";
require_once "Session.php";

class FileManager {
    private $connection;
    private $session;
    
    public function __construct() {
        $connector = new Connector();
        $this->connection = $connector->getConnection();
        $this->session = new Session();
    }
    
    public function hasFreeDownloads($productId) {
        $filesArray = $this->getFiles(FALSE, $productId);
        
        return count($filesArray) > 0;
    }
    
    public function hasPremiumDownloads($productId) {
        $filesArray = $this->getFiles(TRUE, $productId);
        
        return count($filesArray) > 0;
    }
    
    public function getFiles($premium, $productId = -1) {
        if ($productId == -1) {
            $productId = $this->session->getSelectedProductId();
        }
        
        $result = array();
        
        $preparedQuery = $this->connection->prepare("SELECT caption,filename FROM files WHERE productid=? AND premium=?;");
        $preparedQuery->bind_param('di', $productId, $premium);
        $preparedQuery->bind_result($caption, $filename);
        $preparedQuery->execute();
        
        while ($preparedQuery->fetch()) {
            array_push($result, array("caption" => $caption, "filename" => $filename));
        }
        
        return $result;
    }
    
}