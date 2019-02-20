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
$oPage->setTitle(Translations::get('website_name') . ' | ' . Translations::get('admin_page_statistics_countries_title'));
$oPage->setContent( $content );
$oPage->setLevel( '../' );
$oPage->setShowMenu( true );

// show page
echo $oPage->getPage();

function createContent() {
	global $protect, $settings, $oWebuser, $databases;

	// get year
	$year = $protect->requestFieldDefaultMinMax('get', 'year', date("Y"), Settings::get('first_static_year'), date("Y"));
	$check = $protect->requestDigits('get', 'check');

	// title
	$ret = '<h2>' . Translations::get('admin_page_statistics_countries_title') . ' ' . Misc::createYearSwitcher($year,Settings::get('first_static_year'), date("Y")) . '</h2>';

	// download button
	$ret .= '<div class="downloadribbon"><a href="download.php?dataset=countriesperyear&year=' . $year . '" class="button">Download</a></div>';

	require_once("./classes/class_view/class_view.inc.php");
	require_once("./classes/class_view/fieldtypes/class_field_string.inc.php");
	require_once("./classes/class_view/fieldtypes/class_field_date.inc.php");

	$oDb = new class_mysql($databases['default']);
	$oView = new class_view($settings, $oDb);

	if ( $year < Settings::get('first_year') ) {
		$query = "
SELECT `static_statistics_countries`.country_id, name_en, name_nl, geocoder_id, geocoder_year, coordinates, total AS aantal
FROM `countries`
	INNER JOIN `static_statistics_countries` ON `countries`.id=`static_statistics_countries`.country_id
WHERE year=" . $year;
	} else {
		$query = "
SELECT country_id, name_en, name_nl, geocoder_id, geocoder_year, coordinates, count(*) AS aantal
FROM `visitors`
	INNER JOIN `visitor_addresses` ON `visitors`.id=`visitor_addresses`.visitor_id
	INNER JOIN countries ON `visitor_addresses`.country_id=countries.id
WHERE `visitors`.is_deleted=0
	AND `visitor_addresses`.is_deleted=0
	AND `visitor_addresses`.year=" . $year;
	}

	$oView->set_view( array(
		'query' => $query
		, 'count_source_type' => 'query'
		, 'group_by' => 'country_id, name_' . Misc::getLanguage() . ', geocoder_id, geocoder_year, coordinates '
		, 'order_by' => 'aantal DESC, name_' . Misc::getLanguage()
		, 'anchor_field' => 'country_id'
		, 'viewfilter' => true
		, 'table_class' => 'admin'
		, 'show_counter' => true
		, 'hidden_fields' => array( array('year' => $year ) )
		, 'calculate_total' => array('nrofcols' => 3, 'totalcol' => 3, 'field' => 'aantal', 'type' => 'integer')
		));

	$oView->add_field( new class_field_string ( array(
		'fieldname' => 'name_' . Misc::getLanguage()
		, 'fieldlabel' => Translations::get('fld_country')
		, 'viewfilter' => array(
				'filter' => array (
									array (
										'fieldname' => 'name_' . Misc::getLanguage()
										, 'search_in' => 'name_en;name_nl'
										, 'type' => 'string'
										, 'class' => 'quicksearch'
									)
							)
					)
		)));

	$oView->add_field( new class_field_string ( array(
		'fieldname' => 'aantal'
		, 'fieldlabel' => Translations::get('fld_total')
		)));

	if ( $check == 1 ) {
		$oView->add_field( new class_field_string ( array(
			'fieldname' => 'geocoder_id'
			, 'fieldlabel' => 'Code'
			)));

		$oView->add_field( new class_field_string ( array(
			'fieldname' => 'geocoder_year'
			, 'fieldlabel' => 'Year'
			)));

		$oView->add_field( new class_field_string ( array(
			'fieldname' => 'coordinates'
			, 'fieldlabel' => 'Coordinates'
			)));
	}

	// generate view
	$ret .= $oView->generate_view();

	return $ret;
}