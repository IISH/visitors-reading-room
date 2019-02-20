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
$oPage->setTitle(Translations::get('website_name') . ' | ' . Translations::get('admin_page_statistics_month_title'));
$oPage->setContent( $content );
$oPage->setLevel( '../' );
$oPage->setShowMenu( true );

// show page
echo $oPage->getPage();

function createContent() {
	global $protect, $settings, $oWebuser, $databases;

	// title
	$ret = '<h2>' . Translations::get('admin_page_statistics_month_title') . '</h2>';

	// download button
	$ret .= '<div class="downloadribbon"><a href="download.php?dataset=totalspermonth" class="button">Download</a></div>';

	require_once("./classes/class_view/class_view.inc.php");
	require_once("./classes/class_view/fieldtypes/class_field_string.inc.php");
	require_once("./classes/class_view/fieldtypes/class_field_date.inc.php");

	$oDb = new class_mysql($databases['default']);
	$oView = new class_view($settings, $oDb);

	$oView->set_view( array(
		'query' => "
SELECT SUBSTRING(visitor_misc.date_added,1,7) AS _MONTH, count(*) AS _COUNT
FROM `visitors`
	INNER JOIN `visitor_misc` ON `visitors`.id=`visitor_misc`.visitor_id
WHERE `visitors`.is_deleted=0
	AND `visitor_misc`.is_deleted=0
"
		, 'count_source_type' => 'query'
		, 'group_by' => 'SUBSTRING(visitor_misc.date_added,1,7)'
		, 'order_by' => 'SUBSTRING(visitor_misc.date_added,1,7) DESC'
		, 'anchor_field' => ''
		, 'table_class' => 'admin'
		));

	$oView->add_field( new class_field_string ( array(
		'fieldname' => '_MONTH'
		, 'fieldlabel' => Translations::get('fld_month')
	)));

	$oView->add_field( new class_field_string ( array(
		'fieldname' => '_COUNT'
		, 'fieldlabel' => Translations::get('fld_total')
	)));

	// generate view
	$ret .= $oView->generate_view();

	return $ret;
}