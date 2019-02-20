<?php
// TODOGCU
//ini_set('session.gc_maxlifetime', 14*3600);
session_start();

// TODO: tijdelijk. verwijderen error reporting bij livegang
//error_reporting(E_ALL);
//ini_set('display_errors', '1');

//
$settings = array();
require_once dirname(__FILE__) . "/../sites/default/settings.php";

//
if ( !isset($_SESSION["bsrs"]["visitor_loginname"]) ) {
	$_SESSION["bsrs"]["visitor_loginname"] = '';
	// TODO: tijdelijk. verwijderen bsrs bij livegang
	// $_SESSION["bsrs"]["visitor_loginname"] = 'bsrs';
}

//
require_once dirname(__FILE__) . "/adserver.inc.php";
require_once dirname(__FILE__) . "/authentication.inc.php";
require_once dirname(__FILE__) . "/checkbox.inc.php";
require_once dirname(__FILE__) . "/checkboxes.inc.php";
require_once dirname(__FILE__) . "/countries.inc.php";
require_once dirname(__FILE__) . "/country.inc.php";
require_once dirname(__FILE__) . "/file.inc.php";
require_once dirname(__FILE__) . "/goal.inc.php";
require_once dirname(__FILE__) . "/goals.inc.php";
require_once dirname(__FILE__) . "/misc.inc.php";
require_once dirname(__FILE__) . "/mysql.inc.php";
require_once dirname(__FILE__) . "/page.inc.php";
require_once dirname(__FILE__) . "/prevnextdate.inc.php";
require_once dirname(__FILE__) . "/registrations.inc.php";
require_once dirname(__FILE__) . "/settings.inc.php";
require_once dirname(__FILE__) . "/translations.inc.php";
require_once dirname(__FILE__) . "/visitor_research_goal.inc.php";
require_once dirname(__FILE__) . "/visitor_research_goals.inc.php";
require_once dirname(__FILE__) . "/visitor.inc.php";
require_once dirname(__FILE__) . "/visitors.inc.php";
require_once dirname(__FILE__) . "/webuser.inc.php";
require_once dirname(__FILE__) . "/website_protection.inc.php";
require_once dirname(__FILE__) . "/word_manipulations.inc.php";

//
$protect = new WebsiteProtection();

//
$oWebuser = new Webuser($_SESSION["bsrs"]["visitor_loginname"]);

//
//if ( !defined('ENT_XHTML') ) {
//	define('ENT_XHTML', 32);
//}

header('Content-Type: text/html; charset=iso-8859-1');
