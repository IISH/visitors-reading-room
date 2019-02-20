<?php
die('Disabled by GC');

//
require_once "classes/authentication.inc.php";
require_once "classes/misc.inc.php";

$fldLogin = 'FirstnameL';
$fldPassword = '********';

$result_login_check = Authentication::authenticate($fldLogin, $fldPassword);

if ( $result_login_check == 1 ) {
	preprint('Login/password correct.');
} else {
	preprint('Login/password NOT correct.');
}
