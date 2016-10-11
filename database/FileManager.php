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
    
    public function deleteFile($fileId) {
        $preparedQuery = $this->connection->prepare("SELECT filename,productid,premium FROM files WHERE fileid=?;");
        $preparedQuery->bind_param('d', $fileId);
        $preparedQuery->bind_result($fileName, $productId, $premium);
        $preparedQuery->execute();
        
        $preparedQuery->fetch();
        
        if ($premium == 0) {
            unlink("../downloads/free/product" . $productId . "/" . $fileName);
        } else {
            unlink("../downloads/premium/product" . $productId . "/" . $fileName);
        }
        
        $preparedQuery->close();
            
        $deleteQuery = $this->connection->prepare("DELETE FROM files WHERE fileid=? LIMIT 1;");
        $deleteQuery->bind_param('d', $fileId);
        $deleteQuery->execute();
    }
    
    public function getFiles($premium, $productId = -1) {
        if ($productId == -1) {
            $productId = $this->session->getSelectedProductId();
        }
        
        $result = array();
        
        $preparedQuery = $this->connection->prepare("SELECT fileid,caption,filename FROM files WHERE productid=? AND premium=?;");
        $preparedQuery->bind_param('di', $productId, $premium);
        $preparedQuery->bind_result($fileId, $caption, $filename);
        $preparedQuery->execute();
        
        while ($preparedQuery->fetch()) {
            array_push($result, array("fileid" => $fileId, "caption" => $caption, "filename" => $filename));
        }
        
        return $result;
    }
    
}