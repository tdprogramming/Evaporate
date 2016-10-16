<?php

require_once "Connector.php";

class AdminInstaller {
    private $connection;
    
    public function __construct() {
        $connector = new Connector();
        $this->connection = $connector->getConnection();
    }
    
    public function install($rootEmail, $rootPassword) {
        // Filter input
        $rootEmail = filter_var($rootEmail, FILTER_SANITIZE_EMAIL);
        $rootPassword = filter_var($rootPassword, FILTER_SANITIZE_URL);
        
        $rootEmail = $this->connection->real_escape_string($rootEmail);
        $rootPassword = $this->connection->real_escape_string($rootPassword);
        
        $rootPassword = password_hash($rootPassword, PASSWORD_DEFAULT);
        
        $this->connection->real_query("CREATE TABLE users (loginid INTEGER UNSIGNED NOT NULL AUTO_INCREMENT, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, admin BOOLEAN NOT NULL, PRIMARY KEY (loginid));");
        $this->connection->real_query("CREATE TABLE codes (codeid INTEGER UNSIGNED NOT NULL AUTO_INCREMENT, code VARCHAR(50) NOT NULL, issued INTEGER UNSIGNED NOT NULL, usecount INTEGER UNSIGNED NOT NULL, active INTEGER UNSIGNED NOT NULL, uselimit INTEGER UNSIGNED NOT NULL, productid INTEGER UNSIGNED NOT NULL, batchname VARCHAR(50) NOT NULL, batchid INTEGER UNSIGNED NOT NULL, timestamp INTEGER UNSIGNED NOT NULL, PRIMARY KEY (codeid));");
        $this->connection->real_query("CREATE TABLE products (productid INTEGER UNSIGNED NOT NULL AUTO_INCREMENT, title VARCHAR(500) NOT NULL, description VARCHAR(1000) NOT NULL, orderlink VARCHAR(500), redeemlink VARCHAR(500), PRIMARY KEY (productid));");
        $this->connection->real_query("CREATE TABLE files (fileid INTEGER UNSIGNED NOT NULL AUTO_INCREMENT, productid INTEGER UNSIGNED NOT NULL, caption VARCHAR(1000), premium BOOLEAN NOT NULL, filename VARCHAR(300), PRIMARY KEY (fileid));");
        $this->connection->real_query("CREATE TABLE sessions (sessionid INTEGER UNSIGNED NOT NULL AUTO_INCREMENT, loginid INTEGER UNSIGNED NOT NULL, ipaddress VARCHAR(1000), datetime DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, useragent VARCHAR(1000), correctpassword BOOLEAN NOT NULL, PRIMARY KEY (sessionid));");
        
        $preparedQuery = $this->connection->prepare("INSERT INTO users(email, password, admin) VALUES (?,?,1);");
        $preparedQuery->bind_param('ss', $rootEmail, $rootPassword);
        $result = $preparedQuery->execute();
        
        if ($result == FALSE) {
            echo "Install failed, Error = " . $this->connection->error;
        }
    }
    
    public function alreadyInstalled() {
        $result = $this->connection->prepare("SELECT * FROM users;");
        
        if ($result == FALSE) {
            return FALSE;
        }
        
        return TRUE;
    }
}
