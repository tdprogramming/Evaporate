<?php

require_once "Credentials.php";

class Connector {
    private $connection;
    
    public function __construct() {
        // First, check that the user has configured the DBConfig file
        if (Credentials::dbHost == "" || Credentials::dbUserName == "" || Credentials::dbPassword == "" || Credentials::dbName == "") {
            die("Error - database config not setup yet. Please check the documentation and add your settings to the Credentials.php file.");
        }
        
        $this->connection = new mysqli(Credentials::dbHost, Credentials::dbUserName, Credentials::dbPassword, Credentials::dbName);

        // Requires PHP >= 5.2.9 or 5.3.0
        if ($this->connection->connect_error) {
            die("Error - could not connect to database. Please check your Connector.php file, and also the settings for MySQL on your web host. Technical details of error follow:<br/><br/>Connect Error (" . 
                    $this->connection->connect_errno . ") " . $this->connection->connect_error);
        }
    }

    public function getConnection() {
        return $this->connection;
    }
}