<?php
require_once "../classes/start.inc.php";

//
if ( !isset($settings) ) {
	$settings = array();
}

// set language
$_SESSION["language"] = 'en';

// go back
$next = trim($_SERVER['HTTP_REFERER']);
if ( $next == '' ) {
	$next = 'index.php';
}
Misc::goToNextPage($next);
