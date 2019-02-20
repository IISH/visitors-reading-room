<?php
require_once "../classes/start.inc.php";

//
if ( !isset($settings) ) {
	$settings = array();
}

//
$oWebuser->checkLoggedInAsAdminOrSuperadmin();

// drop admin access";
$oWebuser->dropAdminAccess();

// go to registration page
Misc::goToNextPage('../index.php');
