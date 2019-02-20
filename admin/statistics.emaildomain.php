<?php
require_once "../classes/start.inc.php";

//
if ( !isset($settings) ) {
	$settings = array();
}

//
$oWebuser->checkLoggedInAsAdminOrSuperadmin();

// first
$content = createContent();

// then create webpage
$oPage = new Page('../design/page.admin.php', $settings);
$oPage->setTitle(Translations::get('website_name') . ' | ' . Translations::get('admin_page_statistics_emaildomain_title'));
$oPage->setContent( $content );
$oPage->setLevel( '../' );
$oPage->setShowMenu( true );

// show page
echo $oPage->getPage();

function createContent() {
	global $protect, $settings, $oWebuser, $databases;

	// get year
	$year = $protect->requestFieldDefaultMinMax('get', 'year', date("Y"), Settings::get('first_year'), date("Y"));

	// title
	$ret = '<h2>' . Translations::get('admin_page_statistics_emaildomain_title') . ' ' . Misc::createYearSwitcher($year,Settings::get('first_year'), date("Y")) . '</h2>';

	require_once("./classes/class_view/class_view.inc.php");
	require_once("./classes/class_view/fieldtypes/class_field_string.inc.php");
	require_once("./classes/class_view/fieldtypes/class_field_date.inc.php");

	$oDb = new class_mysql($databases['default']);
	$oView = new class_view($settings, $oDb);

//	$excludeDomainNames = "'gmail.com', 'hotmail.com', 'xs4all.nl', 'chello.nl', 'kpnmail.nl', 'upcmail.nl', 'yahoo.fr', 'posteo.de', 'telfort.nl', 'live.nl', 'planet.nl', 'yahoo.com', 'free.fr', 'bluewin.ch'";
//	AND substring(email, LOCATE('@', email)+1) NOT IN ( $excludeDomainNames )

	$oView->set_view( array(
		'query' => "
SELECT substring(email, LOCATE('@', email)+1) AS _DOMAIN, count(*) AS _COUNT
FROM visitors
	INNER JOIN `visitor_misc` ON `visitors`.id=`visitor_misc`.visitor_id
WHERE email IS NOT NULL AND email <> ''
	AND `visitors`.is_deleted=0
	AND `visitor_misc`.is_deleted=0
	AND `year` = " . $year
		, 'count_source_type' => 'query'
		, 'group_by' => 'substring(email, LOCATE(\'@\', email)+1)'
		, 'order_by' => 'count(*) DESC, substring(email, LOCATE(\'@\', email)+1)'
		, 'anchor_field' => ''
		, 'table_class' => 'admin'
		, 'show_counter' => true
		));

	$oView->add_field( new class_field_string ( array(
		'fieldname' => '_DOMAIN'
		, 'fieldlabel' => Translations::get('fld_year')
	)));

	$oView->add_field( new class_field_string ( array(
		'fieldname' => '_COUNT'
		, 'fieldlabel' => Translations::get('fld_total')
	)));

	// generate view
	$ret .= $oView->generate_view();

	return $ret;
}
