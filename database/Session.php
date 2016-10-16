<?php

require_once "Connector.php";
require_once "Credentials.php";
require_once "SessionHistory.php";

class Session {
    private $connection;
    private $sessionHistory;
    
    public function __construct() {
        $connector = new Connector();
        $this->connection = $connector->getConnection();
        $this->sessionHistory = new SessionHistory();
        
        if (session_id() == "") {
            session_start();
        }
        
        if (!isset($_SESSION['userAgent']) || !isset($_SESSION['fingerprint'])) {
            $_SESSION['userAgent'] = sha1($_SERVER['HTTP_USER_AGENT']);
            $_SESSION['fingerprint'] = sha1(Credentials::sessionFingerprint . session_id());
        }
    }
    
    public function login($email, $password) {
        $this->validateSession();
        
	$email = filter_var($email, FILTER_SANITIZE_EMAIL);
        $password = filter_var($password, FILTER_SANITIZE_URL);
        
        $email = $this->connection->real_escape_string($email);
        $password = $this->connection->real_escape_string($password);
                
        $preparedQuery = $this->connection->prepare("SELECT loginid,password FROM users WHERE email = ? LIMIT 1;");
        $preparedQuery->bind_param('s', $email);
        $preparedQuery->execute();
        $preparedQuery->bind_result($loginId, $hashedPassword);
 
        if ($preparedQuery->fetch()) {
            if (password_verify($password, $hashedPassword)) {
                $_SESSION['loginid'] = $loginId;
                $this->sessionHistory->logSession($loginId, TRUE);
            } else {
                $this->sessionHistory->logSession($loginId, FALSE);
            }
        }
    }
    
    public function getLoginId() {
        $this->validateSession();
        return $_SESSION['loginid'];
    }
    
    public function validateSession() {
        if ($_SESSION['userAgent'] != sha1($_SERVER['HTTP_USER_AGENT'])) {
            die("Invalid Session.");
        }
        if ($_SESSION['fingerprint'] != sha1(Credentials::sessionFingerprint . session_id())) {
            die("Invalid Session.");
        }
    }
    
    public function isLoggedIn() {
        $this->validateSession();
        return isset($_SESSION['loginid']);
    }
    
    public function logout() {
        $this->validateSession();
        unset($_SESSION['loginid']);
    }
    
    public function setSelectedProductId($newValue) {
        $this->validateSession();
        $_SESSION["selectedProductId"] = $newValue;
    }
    
    public function getSelectedProductId() {
        $this->validateSession();
        return $_SESSION["selectedProductId"];
    }
    
    public function setSelectedProductCode($newValue) {
        $this->validateSession();
        $_SESSION["selectedProductCode"] = $newValue;
    }
    
    public function getSelectedProductCode() {
        $this->validateSession();
        return $_SESSION["selectedProductCode"];
    }
}
