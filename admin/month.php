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
// TODO: als geen querystring dan geen date(...
$oPage->setTitle(Translations::get('website_name') . ' | ' . Translations::get('admin_page_month_title') . ' '. date('Y-m'));
$oPage->setContent( $content );
$oPage->setLevel( '../' );
$oPage->setShowMenu( true );

// show page
echo $oPage->getPage();

function createContent() {
	global $protect, $settings, $oWebuser, $databases;

	// get month
	// TODO: add default/min/max protection
	$month = $protect->request('get', 'month');
	if ( $month == '' ) {
		$month = date("Y-m");
	}
	$month = substr($month, 0, 7);

	//
	$year = substr($month, 0, 4);

	// title
	$ret = '<h2>' . Misc::createMonthSwitcher($month,Settings::get('first_year') . '-01', date("Y-m")) . '</h2>';

	require_once("./classes/class_view/class_view.inc.php");
	require_once("./classes/class_view/fieldtypes/class_field_string.inc.php");
	require_once("./classes/class_view/fieldtypes/class_field_date.inc.php");

	$oDb = new class_mysql($databases['default']);
	$oView = new class_view($settings, $oDb);

	$oView->set_view( array(
		'query' => "
SELECT visitors.id, visitor_misc.firstname, visitor_misc.lastname, visitors.email, visitor_misc.date_added
FROM `visitors`
	INNER JOIN `visitor_misc` ON `visitors`.id=`visitor_misc`.visitor_id
WHERE `visitors`.is_deleted=0
	AND `visitor_misc`.is_deleted=0
	AND `visitor_misc`.date_added LIKE '" . $month . "-%' "
		, 'count_source_type' => 'query'
		, 'order_by' => 'lastname, firstname, visitor_misc.date_added DESC'
		, 'anchor_field' => 'id'
		, 'viewfilter' => true
		, 'table_class' => 'admin'
		, 'show_counter' => true
		, 'hidden_fields' => array( array('month' => $month ) )
		));

	$oView->add_field( new class_field_string ( array(
		'fieldname' => 'firstname'
		, 'fieldlabel' => Translations::get('fld_firstname')
		, 'if_no_value' => '-no value-'
		, 'href' => 'registration.php?id=[FLD:id]&year=' . $year . '&backurl=[BACKURL]'
		, 'viewfilter' => array(
				'filter' => array (
									array (
										'fieldname' => 'firstname'
										, 'search_in' => 'firstname'
										, 'type' => 'string'
										, 'class' => 'quicksearch'
									)
							)
					)
		)));

	$oView->add_field( new class_field_string ( array(
		'fieldname' => 'lastname'
		, 'fieldlabel' => Translations::get('fld_lastname')
		, 'if_no_value' => '-no value-'
		, 'href' => 'registration.php?id=[FLD:id]&year=' . $year . '&backurl=[BACKURL]'
		, 'viewfilter' => array(
				'filter' => array (
								array (
								'fieldname' => 'lastname'
								, 'search_in' => 'lastname'
								, 'type' => 'string'
								, 'class' => 'quicksearch'
								)
						)
				)
		)));

	$oView->add_field( new class_field_date ( array(
		'fieldname' => 'email'
		, 'fieldlabel' => Translations::get('fld_email')
		, 'viewfilter' => array(
				'filter' => array (
						array (
							'fieldname' => 'email'
							, 'search_in' => 'email'
							, 'type' => 'string'
							, 'class' => 'quicksearch'
							)
					)
			)
		)));

	$oView->add_field( new class_field_date ( array(
		'fieldname' => 'date_added'
		, 'fieldlabel' => Translations::get('fld_datetimeadded')
		, 'format' => 'Y-m-d H:i:s'
		, 'viewfilter' => array(
				'filter' => array (
						array (
							'fieldname' => 'revision'
							, 'search_in' => 'visitor_misc.date_added'
							, 'type' => 'string'
							, 'class' => 'quicksearch'
							)
					)
			)
		)));

	// generate view
	$ret .= $oView->generate_view();

	return $ret;
}