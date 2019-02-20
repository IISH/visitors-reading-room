<?php
require_once "classes/start.inc.php";

//
if ( !isset($settings) ) {
	$settings = array();
}

//
$oWebuser->checkLoggedIn();

// go to next page
Misc::goToNextPage('choose_language.php');
