<?php

require_once "adminheader.php";
require_once "../database/UserManager.php";

$userManager = new UserManager();

$passwordChanged = FALSE;

if (isset($_POST["cmdchangepassword"])) {
    $userManager->changePassword(filter_input(INPUT_POST, "currentpassword", FILTER_SANITIZE_STRING), 
        filter_input(INPUT_POST, "newpassword", FILTER_SANITIZE_STRING), filter_input(INPUT_POST, "confirmnewpassword", FILTER_SANITIZE_STRING));
    $passwordChanged = TRUE;
}

if (!$passwordChanged) {
?>
    
<form class="w3-form" name="change-password-form" id="change-password-form" method="post" action="<?php 
    $selfURL = filter_input(INPUT_SERVER, "PHP_SELF", FILTER_SANITIZE_URL);
    echo $selfURL;
    ?>"> 
    
    <p>
        <label class="w3-label">Current Password</label>
        <input class="w3-input" tabindex="1" accesskey="c" name="currentpassword" type="text" maxlength="50" id="currentpassword" /> 
    </p>

    <p>
        <label class="w3-label">New Password</label>
        <input class="w3-input" tabindex="2" accesskey="n" name="newpassword" type="text" maxlength="50" id="newpassword" /> 
    </p>

    <p>
        <label class="w3-label">Confirm New Password</label>
        <input class="w3-input" tabindex="3" accesskey="o" name="confirmnewpassword" type="text" maxlength="50" id="confirmnewpassword" /> 
    </p>

    <p>
        <input class="w3-btn" tabindex="3" accesskey="l" type="submit" name="cmdchangepassword" value="Change Password" />
    </p>
</form>
<?php } else { ?>
<p>
    Your password has been changed.
</p>

<p>
    <a class="w3-btn" href="index.php">Home</a>
</p>
<?php } ?>