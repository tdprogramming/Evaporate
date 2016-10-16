<?php

require_once "Connector.php";
require_once "Credentials.php";
require_once "Session.php";

class UserManager {
    private $connection;
    private $session;
    
    public function __construct() {
        $connector = new Connector();
        $this->connection = $connector->getConnection();
        $this->session = new Session();
    }
    
    public function getLoginIdFromEmail($email) {
        $preparedQuery = $this->connection->prepare("SELECT loginid FROM users WHERE email=?;");
        $preparedQuery->bind_param('s', $email);
        $preparedQuery->bind_result($loginId);
        $preparedQuery->execute();
        
        if ($preparedQuery->fetch()) {
            return $loginId;
        }
        
        return -1;
    }
    
    public function changePassword($oldPassword, $newPassword, $confirmNewPassword) {
        if (!$this->session->isLoggedIn()) {
            die("Not logged in");
        }
        
        if ($newPassword != $confirmNewPassword) {
            die("New passwords don't match. Please go back and try again.");
        }
        
        $hashedPassword = password_hash($oldPassword, PASSWORD_DEFAULT);
        $loginId = $this->session->getLoginId();
        
        $preparedQuery = $this->connection->prepare("SELECT email FROM users WHERE loginid=? AND password=?;");
        $preparedQuery->bind_param('ds', $loginId, $hashedPassword);
        $preparedQuery->bind_result($userEmail);
        $preparedQuery->execute();
        
        if ($preparedQuery->fetch()) {
            $preparedQuery->close();
            $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $preparedQuery = $this->connection->prepare("UPDATE users SET password=? WHERE loginid=?;");
            $preparedQuery->bind_param('sd', $hashedNewPassword, $loginId);
            $preparedQuery->execute();
        } else {
            die("Wrong current password");
        }
    }
    
    public function issueTemporaryPassword($userEmail) {
        $this->session->validateSession();
        
        $userEmail = filter_var($userEmail, FILTER_SANITIZE_EMAIL);
        $userEmail = $this->connection->real_escape_string($userEmail);
        
        $newPassword = substr(md5(rand()), 0, 7);
        
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $preparedQuery = $this->connection->prepare("UPDATE users SET password=? WHERE email=?;");
        $preparedQuery->bind_param('ss', $hashedPassword, $userEmail);
        $result = $preparedQuery->execute();
        
        if ($result == FALSE) {
            return;
        }

        $headers = "From: " . Credentials::automatedEmail . "\r\n" .
            "Reply-To: " . Credentials::automatedEmail;

        $subject = "Temporary Evaporate password";
        $message = "You requested a temporary password for your Evaporate user account.\r\nPlease login with this password, and for security immediately change it.\r\nYour temporary password is: "
                . $newPassword . "\r\n\r\nPlease do not reply to this email. This inbox is not monitored.";
        $message = wordwrap($message, 70, "\r\n");
        mail($userEmail, $subject, $message, $headers);
    }
}
