<?php
require_once "classes/start.inc.php";

//
$email = $protect->request('get', "email");
if ( $email == '' ) {
	return '';
} else {
	// max length
	$email = substr($email, 0, 255);
}

//
$id = $protect->requestDigits('get', "id");
if ( $id == '' ) {
	$id = 0;
}

//
if ( $id != 0 ) {
	$lastYear = Visitors::isEmailAlreadyUsedByAnotherVisitor($email, $id);
} else {
	$lastYear = Visitors::yearEmailAddressIsLastUsed($email);
}

//
echo $lastYear;

// save email address in a temporary table for 'checking' purposes
$oConn = new class_mysql($databases['default']);
$oConn->connect();
$datetime = date("Y-m-d H:i:s");
$ipAddress = Misc::getIpAddress();
$queryEmail = "INSERT INTO `email_checks` (`email`, `last_year`, `datetime`, `ip_address`) VALUES ('" . addslashes($email) . "', '$lastYear', '$datetime', '$ipAddress');";
$result = mysql_query($queryEmail, $oConn->getConnection());
