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
        $filesArray = $this->getFilesWithCost($productId, FALSE);
        
        return count($filesArray) > 0;
    }
    
    public function hasPremiumDownloads($productId) {
        $filesArray = $this->getFilesWithCost($productId, TRUE);
        
        return count($filesArray) > 0;
    }
    
    public function deleteFile($fileId) {
        $preparedQuery = $this->connection->prepare("SELECT filename,productid,premium FROM files WHERE fileid=?;");
        $preparedQuery->bind_param('d', $fileId);
        $preparedQuery->bind_result($fileName, $productId, $premium);
        $preparedQuery->execute();
        
        $preparedQuery->fetch();
        
        unlink("../downloads/product" . $productId . "/" . $fileName);
        
        $preparedQuery->close();
            
        $deleteQuery = $this->connection->prepare("DELETE FROM files WHERE fileid=? LIMIT 1;");
        $deleteQuery->bind_param('d', $fileId);
        $deleteQuery->execute();
    }
    
    public function updateFileCaption($fileId, $fileCaption) {
        $preparedQuery = $this->connection->prepare("UPDATE files SET caption=? WHERE fileid=?;");
        $preparedQuery->bind_param('sd', $fileCaption, $fileId);
        $preparedQuery->execute();
    }
    
    public function isFilePremium($fileName, $productId = -1) {
        if ($productId == -1) {
            $productId = $this->session->getSelectedProductId();
        }
        
        $preparedQuery = $this->connection->prepare("SELECT premium FROM files WHERE productid=? AND filename=?;");
        $preparedQuery->bind_param('ds', $productId, $fileName);
        $preparedQuery->bind_result($premium);
        $preparedQuery->execute();
        $preparedQuery->fetch();
        return $premium;
    }
    
    public function changePremiumStatus($fileId, $newStatus) {
        $preparedQuery = $this->connection->prepare("UPDATE files SET premium=? WHERE fileid=?;");
        $preparedQuery->bind_param('dd', $newStatus, $fileId);
        $preparedQuery->execute();
    }
    
    public function getFilesWithCost($productId = -1, $isPremium = FALSE) {
        if ($productId == -1) {
            $productId = $this->session->getSelectedProductId();
        }
        
        $result = array();
        
        $preparedQuery = $this->connection->prepare("SELECT fileid,caption,filename FROM files WHERE productid=? AND premium=?;");
        $preparedQuery->bind_param('dd', $productId, $isPremium);
        $preparedQuery->bind_result($fileId, $caption, $filename);
        $preparedQuery->execute();
        
        while ($preparedQuery->fetch()) {
            array_push($result, array("fileid" => $fileId, "caption" => $caption, "filename" => $filename));
        }
        
        return $result;
    }
    
    public function getFiles($productId = -1) {
        if ($productId == -1) {
            $productId = $this->session->getSelectedProductId();
        }
        
        $result = array();
        
        $preparedQuery = $this->connection->prepare("SELECT fileid,caption,filename,premium FROM files WHERE productid=?;");
        $preparedQuery->bind_param('d', $productId);
        $preparedQuery->bind_result($fileId, $caption, $filename, $premium);
        $preparedQuery->execute();
        
        while ($preparedQuery->fetch()) {
            array_push($result, array("fileid" => $fileId, "caption" => $caption, "filename" => $filename, "premium" => $premium));
        }
        
        return $result;
    }
    
}