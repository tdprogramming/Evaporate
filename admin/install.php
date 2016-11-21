<?php
require_once '../database/AdminInstaller.php';

$adminInstaller = new AdminInstaller();
$installError = null;
$installSuccess = FALSE;

if ($adminInstaller->alreadyInstalled()) {
    die("Already installed.");
}

if (isset($_POST['cmdsetadmin'])) {
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_URL);
    $confirmPassword = filter_input(INPUT_POST, 'confirmpassword', FILTER_SANITIZE_URL);
    
    if ($password != $confirmPassword) {
        $installError = "Error: Passwords don't match. Please try again.";
    }
    
    if (!$installError) {
        $adminInstaller->install(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL), filter_input(INPUT_POST, 'password', FILTER_SANITIZE_URL));
        $installSuccess = TRUE;
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Installer</title>
    </head>
    <link rel="stylesheet" href="../css/w3.css">
    <body>
<?php
if ($installSuccess) {
    ?>
        <div class="w3-container w3-blue">
            <h2>Installation Complete</h2>
        </div>

        <div class=""w3-container"></div>
            <a class="w3-btn" href="login.php">Click here to login</a>
        </div>
    <?php        
} else {
        ?>
        <div class="w3-container w3-blue">
            <h2>Welcome to the Installer</h2>
        </div>
        <div class="w3-container">
            <p>
                <?php echo ($installError ? $installError : "Please enter an admin email address and password."); ?>
            </p>
        </div>
        <form class="w3-form" name="register-form" id="register-form" method="post" action="<?php 
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
                <label class="w3-label">Confirm Password</label>
                <input class="w3-input" tabindex="3" accesskey="c" name="confirmpassword" type="password" maxlength="15" id="confirmpassword" /> 
            </p>

            <p>
                <input class="w3-btn" tabindex="4" accesskey="l" type="submit" name="cmdsetadmin" value="Install" />
            </p> 
        </form>
<?php } ?>
    </body>
</html>
            