<?php

require_once '../database/Session.php';
require_once '../database/CodeManager.php';

$session = new Session();
$loggedIn = FALSE;
$loginError = "";

if ($session->isLoggedIn()) {
    $loggedIn = TRUE;
}
else {
    if (isset($_POST['cmdlogin'])) {
        $session->login(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL), filter_input(INPUT_POST, 'password', FILTER_SANITIZE_URL));
        
        if ($session->isLoggedIn()) {
            $loggedIn = TRUE;
        } else {
            $loggedIn = FALSE;
            $loginError = "Invalid Email and/or password. Please try again.";
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
        
        <form class="w3-form" name="login-form" id="login-form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>"> 
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
    </body>
<?php
}