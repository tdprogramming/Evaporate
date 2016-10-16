<?php

require_once '../database/Session.php';
require_once '../database/CodeManager.php';
require_once '../database/UserManager.php';
require_once '../database/SessionHistory.php';

$session = new Session();
$sessionHistory = new SessionHistory();
$loggedIn = FALSE;
$loginError = "";
$userManager = new UserManager();

if ($session->isLoggedIn()) {
    $loggedIn = TRUE;
}
else {
    if (isset($_POST["cmdlogin"])) {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        
        $loginId = $userManager->getLoginIdFromEmail($email);
        $loggedIn = FALSE;
        $loginError = "Invalid Email and/or password. Please try again.";
        
        if ($loginId != -1) {
            if ($sessionHistory->hasTooManyBadLogins($loginId)) {
                $dateTime = $sessionHistory->nextAvailableLoginTime($loginId);
                $loginError = "Too many bad logins. You can try to login again at " . $dateTime . ".";
            } else {
                $session->login($email, filter_input(INPUT_POST, 'password', FILTER_SANITIZE_URL));
        
                if ($session->isLoggedIn()) {
                    $loggedIn = TRUE;
                    $loginError = "";
                }
            }
        }
    }
}

if ($loggedIn == TRUE) {
    header("Location: index.php");
    echo "Redirecting you to admin home...";
} else {
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Please Login</title>
    </head>
    <link rel="stylesheet" href="../css/w3.css">
    <body>
        <?php
            if ($loginError) {
                ?>
                <div class="w3-container w3-red">
                    <h2><?php echo $loginError; ?></h2>
                </div>
                <?php
            } else {
                ?>
                <div class="w3-container w3-blue">
                    <h2>Please Login</h2>
                </div>
                <?php
            }
        ?>
        
        <form class="w3-form" name="login-form" id="login-form" method="post" action="<?php 
            $selfURL = filter_input(INPUT_SERVER, "PHP_SELF", FILTER_SANITIZE_URL);
            echo $selfURL;
            ?>"> 
            <p>
                <label class="w3-label">Email Address</label>
                <input class="w3-input" tabindex="1" accesskey="e" name="email" type="text" maxlength="50" id="email" /> 
            </p>

            <p>
                <label class="w3-label">Password</label>
                <input class="w3-input" tabindex="2" accesskey="p" name="password" type="password" maxlength="15" id="password" /> 
            </p>

            <p>
                <input class="w3-btn" tabindex="3" accesskey="l" type="submit" name="cmdlogin" value="Login" />
            </p>
        </form>
        
        <p>
            <a class="w3-btn" href="forgotpassword.php">Forgot Password</a>
        </p>
    </body>
<?php
}