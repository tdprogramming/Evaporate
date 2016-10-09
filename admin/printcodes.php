<?php

require_once 'adminheader.php';

$STATUS_CODE_SUCCEEDED = "status_code_succeeded";
$STATUS_CODE_BAD_INDICES = "status_too_many_codes";

if (isset($_GET["productid"])) {
    $session->setSelectedProductId($_GET["productid"]);
}

if (isset($_POST['cmdprint'])) {
    $codeManager = new CodeManager();
    $printOut = $codeManager->printCodes(filter_input(INPUT_POST, 'fromcode', FILTER_SANITIZE_NUMBER_INT), filter_input(INPUT_POST, 'tocode', FILTER_SANITIZE_NUMBER_INT));
    echo $printOut;
} else {

?>

<form name="login-form" id="print-form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>"> 
  <fieldset> 
  <legend>Issue Codes:</legend> 
  <dl> 
    <dt> 
      <label title="From Code:">From Code:
      <input tabindex="1" accesskey="f" name="fromcode" type="text" maxlength="50" id="fromcode" /> 
      </label> 
    </dt> 
  </dl> 
  <dl> 
    <dt> 
      <label title="To Code:">To Code:
      <input tabindex="2" accesskey="t" name="tocode" type="text" maxlength="50" id="tocode" /> 
      </label> 
    </dt> 
  </dl> 
  <dl> 
    <dt> 
      <label title="Submit"> 
      <input tabindex="3" accesskey="l" type="submit" name="cmdprint" value="Print" /> 
      </label> 
    </dt> 
  </dl> 
  </fieldset> 
</form>

<?php
}