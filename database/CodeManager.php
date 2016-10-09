<?php

require_once "Connector.php";
require_once "Session.php";

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
        
        $codeIndex = $this->getHighestCodeIndex();
        
        $preparedQuery = $this->connection->prepare("INSERT INTO codes(code, issued, usecount, active, uselimit, codeindex, productid) VALUES (?,0,0,0,0,?,?);");
        $preparedQuery->bind_param('sdd', $codeString, $codeIndex, $productId);
        
        for ($i = 0; $i < $numCodes; $i++) {
            $codeIndex++;
            $codeString = substr(md5(rand()), 0, 7);
            $preparedQuery->execute();
        }
    }
    
    private function getHighestCodeIndex() {
        $session = new Session();
        $productId = $session->getSelectedProductId();
        
        $preparedQuery = $this->connection->prepare("SELECT codeindex FROM codes WHERE productid = ?;");
        $preparedQuery->bind_param('d', $productId);
        $preparedQuery->bind_result($codeindex);
        $preparedQuery->execute();
        
        $highestCodeIndex = 0;
        
        while ($preparedQuery->fetch()) {
            if ($codeIndex > $highestCodeIndex) {
                $highestCodeIndex = $codeIndex;
            }
        }
        
        return $highestCodeIndex;
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
    
    public function printCodes($firstCode, $lastCode) {
        $result = "";
        
        $session = new Session();
        $productId = $session->getSelectedProductId();
        $this->productManager->fetchCurrentProduct();
        
        $firstCode = $this->connection->real_escape_string($firstCode);
        $lastCode = $this->connection->real_escape_string($lastCode);
        
        // Select set of codes between the 2 code indices - this will need to be updated if we get to a situation where codes can be deleted
        $preparedQuery = $this->connection->prepare("SELECT code FROM codes WHERE codeindex >= ? AND codeindex <= ? && productid = ?;");
        $preparedQuery->bind_param('ddd', $firstCode, $lastCode, $productId);
        $preparedQuery->bind_result($code);
        $preparedQuery->execute();
        
        if ($preparedQuery) {
            while ($preparedQuery->fetch())
            {
                $result .= "----------------------------------------------<br /><br />";
                $result .= "Your download code is: " . $code . "<br /><br />";
                $result .= "<br /><br />";
                $result .= "Go to " . $this->producManager->getRedeemLink() . " to redeem your code.<br /><br />";
            }
        }
        
        return $result;
    }
}
