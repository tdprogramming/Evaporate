<?php
require_once '../database/UserManager.php';

$userManager = new UserManager();
$passwordSent = FALSE;

if (isset($_POST["cmdsubmit"])) {
    $userManager->issueTemporaryPassword(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
    $passwordSent = TRUE;
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Forgot Password</title>
    </head>
    <link rel="stylesheet" href="../css/w3.css">
    <link rel="stylesheet" href="../css/links.css">
    <body>
        <div class="w3-container w3-blue">
            <span class="w3-left"><h2>Forgot Password</h2></span>
        </div>
 
        <?php
            if ($passwordSent) {
                ?>
                <div class="w3-container">
                    <p>
                        Thank you. If you email address has a user account with us then a temporary password will have been emailed to you.
                    </p>
                    <p>
                        <a class="w3-btn" href="login.php">Back to Login</a>
                    </p>
                </div>
                <?php
            } else {
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
                <input class="w3-btn" tabindex="3" accesskey="l" type="submit" name="cmdsubmit" value="Submit" />
            </p>
        </form>
        
            <?php } ?>
    </body>
</html>
