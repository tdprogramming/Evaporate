<?php

require_once "Connector.php";
require_once "Session.php";
require_once "ProductManager.php";

class CodeManager {
    private $connection;
    private $productManager;
    private $session;
    
    public function __construct() {
        $connector = new Connector();
        $this->connection = $connector->getConnection();
        $this->session = new Session();
        $this->productManager = new ProductManager();
    }
    
    public function updateBatchName($batchId, $batchName) {
        $preparedQuery = $this->connection->prepare("UPDATE codes SET batchname=? WHERE batchid=?;");
        $preparedQuery->bind_param('sd', $batchName, $batchId);
        $preparedQuery->execute();
    }
    
    public function generateCodes($numCodes) {
        $options = array(
            'options' => array(
            'default' => 0, // value to return if the filter fails
        ));
        
        $session = new Session();
        $productId = $session->getSelectedProductId();
        
        $numCodes = filter_var($numCodes, FILTER_VALIDATE_INT, $options);

        if ($numCodes > 500) {
            die("Can't create more than 500 codes at a time");
        }
        
        $batchId = $this->getNextBatchId();
        $batchName = "Batch " . $batchId;
        $date = new DateTime();
        $timeStamp = $date->getTimestamp();
        $codeString = "";
        
        $preparedQuery = $this->connection->prepare("INSERT INTO codes(code, issued, usecount, active, uselimit, productid, batchname, batchid, timestamp) VALUES (?,0,0,0,0,?,?,?,?);");
        $preparedQuery->bind_param('sdsdd', $codeString, $productId, $batchName, $batchId, $timeStamp);
        
        for ($i = 0; $i < $numCodes; $i++) {
            $codeString = substr(md5(rand()), 0, 7);
            $preparedQuery->execute();
        }
    }
    
    private function getNextBatchId() {
        $session = new Session();
        
        $preparedQuery = $this->connection->prepare("SELECT DISTINCT batchid FROM codes;");
        $preparedQuery->bind_result($batchId);
        $preparedQuery->execute();
        
        $highestBatchId = -1;
        
        while ($preparedQuery->fetch()) {
            if ($batchId > $highestBatchId) {
                $highestBatchId = $batchId;
            }
        }
        
        return $highestBatchId + 1;
    }
    
    public function isCodeValid($code) {
        $code = $this->sanitizeInputCode($code);
        $code = $this->connection->real_escape_string($code);
        $productId = $this->session->getSelectedProductId();
        
        $preparedQuery = $this->connection->prepare("SELECT * FROM codes WHERE code = ? AND productid = ? LIMIT 1;");
        $preparedQuery->bind_param('sd', $code, $productId);
        $preparedQuery->execute();
        
        if ($preparedQuery->fetch())
        {
            return TRUE;
        }
        
        return FALSE;
    }

    private function sanitizeInputCode($code) {
        $result = "";
        $legalCharacters = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        $codeLength = strlen($code);
        
        for ($i = 0; $i < $codeLength; $i++) {
            $char = substr($code, $i, 1);
            
            if (strpos($legalCharacters, $char) != FALSE) {
                $result = $result . $char;
            }
        }
        
        return $result;
    }
    
    public function printCodes($batchId) {
        $fpdf = new FPDF();
        $fpdf->AddPage();
        $fpdf->SetFont('Arial','I',8);
        $this->productManager->fetchCurrentProduct();
        $batchId = $this->connection->real_escape_string($batchId);
        
        // Select set of codes between the 2 code indices - this will need to be updated if we get to a situation where codes can be deleted
        $preparedQuery = $this->connection->prepare("SELECT code FROM codes WHERE batchid = ?;");
        $preparedQuery->bind_param('d', $batchId);
        $preparedQuery->bind_result($code);
        $preparedQuery->execute();
        
        if ($preparedQuery) {
            while ($preparedQuery->fetch())
            {
                $codeString = "Your download code is: " . $code . "\n";
                $codeString .= "Go to " . $this->productManager->getRedeemLink() . " to redeem your code.";
                $fpdf->MultiCell(80, 10, $codeString, 1, "C");
            }
        }
        
        $fpdf->Output("D");
    }
    
    public function fetchAllCodeBatches() {
        $productId = $this->session->getSelectedProductId();
        
        $result = array();
        
        $preparedQuery = $this->connection->prepare("SELECT DISTINCT batchid, batchname FROM codes WHERE productid = ?;");
        
        if (!$preparedQuery) {
            die("Error fetching codes");
        }
        
        $preparedQuery->bind_param('d', $productId);
        $preparedQuery->bind_result($batchId, $batchName);
        $preparedQuery->execute();
        
        while ($preparedQuery->fetch()) {
            array_push($result, array("batchid" => $batchId, "batchname" => $batchName));
        }
        
        return $result;
    }
}
