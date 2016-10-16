<?php

require_once "Session.php";

class SessionHistory {
    const MAX_BAD_LOGINS = 5;
    const BAD_LOGIN_LOCKOUT = "P1D";    // Wait Period 1 Day after too many bad logins to unlock
    private $connection;
    
    public function __construct() {
        $connector = new Connector();
        $this->connection = $connector->getConnection();
    }
    
    public function logSession($loginId, $correctPassword) {
        $preparedQuery = $this->connection->prepare("INSERT INTO sessions(loginid, ipaddress, useragent, correctpassword) VALUES (?,?,?,?);");
        $preparedQuery->bind_param("dssd", $loginId, $ipAddress, $userAgent, $correctPassword);
        $preparedQuery->execute();
    }
    
    public function hasTooManyBadLogins($loginId) {
        $preparedQuery = $this->connection->prepare("SELECT correctpassword FROM sessions WHERE loginid=? ORDER BY datetime DESC");
        $preparedQuery->bind_param("d", $loginId);
        $preparedQuery->bind_result($correctPassword);
        $preparedQuery->execute();
        
        if ($preparedQuery->num_rows == 0) {
            return FALSE;
        }
        
        for ($i = 0; $i < SessionHistory::MAX_BAD_LOGINS; $i++) {
            $preparedQuery->fetch();
            if ($correctPassword == TRUE) {
                return FALSE;
            }
        }
        
        return TRUE;
    }
    
    /**
     * Only call this function when satisfied that the user has entered too many bad logins
     * @param type $loginId
     */
    public function nextAvailableLoginTime($loginId) {
        $preparedQuery = $this->connection->prepare("SELECT datetime FROM sessions WHERE loginid=? ORDER BY datetime DESC");
        $preparedQuery->bind_result($dateTime);
        
        $castDateTime = new DateTime($dateTime);
        
        return $dateTime->add(new DateInterval(SessionHistory::BAD_LOGIN_LOCKOUT));
    }
}
