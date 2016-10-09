<?php

require_once '../database/Session.php';

$session = new Session();

$session->logout();

echo "Logged out. Redirecting you to the login page...";
header("Location: index.php");