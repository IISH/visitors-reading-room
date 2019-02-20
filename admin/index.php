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
$oPage->setTitle(Translations::get('website_name') . ' | ' . Translations::get('admin_page_day_title') . ' ' . date('Y-m-d'));
$oPage->setContent( $content );
$oPage->setLevel( '../' );
$oPage->setShowMenu( true );

// show page
echo $oPage->getPage();

function createContent() {
	global $protect, $settings, $oWebuser, $databases;

	// get month
	// TODO: add default/min/max protection
	$day = $protect->request('get', 'day');
	if ( $day == '' ) {
		$day = date("Y-m-d");
	}
	$day = substr($day, 0, 10);

	//
	$year = substr($day, 0, 4);

	// title
	$ret = '<h2>' . Misc::createDaySwitcher($day,Settings::get('first_year') . '-01-01', date("Y-m-d")) . '</h2>';

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
	AND `visitor_misc`.date_added LIKE '" . $day . "%' "
		, 'count_source_type' => 'query'
		, 'order_by' => 'visitor_misc.date_added DESC, lastname, firstname'
		, 'anchor_field' => 'id'
		, 'viewfilter' => true
		, 'table_class' => 'admin'
		, 'show_counter' => true
		, 'hidden_fields' => array( array('day' => $day ) )
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
		, 'fieldlabel' => Translations::get('fld_timeadded')
		, 'format' => 'H:i:s'
		)));

	// generate view
	$ret .= $oView->generate_view();

	return $ret;
}