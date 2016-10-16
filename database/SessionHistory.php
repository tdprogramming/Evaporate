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
    
    public function getConsecutiveBadLogins($loginId) {
        $preparedQuery = $this->connection->prepare("SELECT correctpassword FROM sessions WHERE loginid=? ORDER BY datetime DESC");
        $preparedQuery->bind_param("d", $loginId);
        $preparedQuery->bind_result($correctPassword);
        $preparedQuery->execute();
        $preparedQuery->store_result();

        $result = 0;
        $numRows = $preparedQuery->num_rows;
        
        for ($i = 0; $i < $numRows; $i++) {
            $preparedQuery->fetch();
            
            $result++;
            
            if ($correctPassword == TRUE) {
                return $result;
            }
        }
        
        return $result;
    }
    
    public function hasTooManyBadLogins($loginId) {
        if ($this->getConsecutiveBadLogins($loginId) > SessionHistory::MAX_BAD_LOGINS) {
            $dateTime = $this->nextAvailableLoginTime($loginId);
            $dateTimeNow = new DateTime();
            
            if ($dateTime->getTimestamp() < $dateTimeNow->getTimestamp()) {
                return FALSE;   // can try to login again
            }
            
            return TRUE;
        }
        
        return FALSE;
    }
    
    /**
     * Only call this function when satisfied that the user has entered too many bad logins
     * @param type $loginId
     */
    public function nextAvailableLoginTime($loginId) {
        $preparedQuery = $this->connection->prepare("SELECT datetime FROM sessions WHERE loginid=? ORDER BY datetime DESC");
        $preparedQuery->bind_result($dateTime);
        
        $castDateTime = new DateTime($dateTime);
        
        return $castDateTime->add(new DateInterval(SessionHistory::BAD_LOGIN_LOCKOUT));
    }
    
    public function getNumSessions($loginId) {
        $preparedQuery = $this->connection->prepare("SELECT * FROM sessions WHERE loginid=?;");
        $preparedQuery->bind_param("d", $loginId);
        $preparedQuery->execute();
        
        return $preparedQuery->num_rows;
    }
    
    public function getSessionsArray($startIndex, $numRows) {
        
    }
}
