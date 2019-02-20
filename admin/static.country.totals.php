<?php
require_once "../classes/start.inc.php";

//
if ( !isset($settings) ) {
	$settings = array();
}

//
$oWebuser->checkLoggedInAsSuperadmin();

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

	$check = $protect->requestDigits('get', 'check');
	$urlCheck = '';
	if ( $check == 1 ) {
		$urlCheck = '&check=' . $check;
	}

	// get year
//	$year = $protect->requestFieldDefaultMinMax('get', 'year', date("Y"), Settings::get('first_year'), date("Y"));
	$vf_year = $protect->request('get', 'vf_year');

	// title
	$ret = '<h2>' . Translations::get('admin_page_statistics_countries_title') . '</h2>';

	require_once("./classes/class_view/class_view.inc.php");
	require_once("./classes/class_view/fieldtypes/class_field_string.inc.php");
	require_once("./classes/class_view/fieldtypes/class_field_date.inc.php");

	$oDb = new class_mysql($databases['default']);
	$oView = new class_view($settings, $oDb);

	$oView->set_view( array(
		'query' => "
SELECT static_statistics_countries.id, `year`, name_en, name_nl, geocoder_id, geocoder_year, coordinates, total
FROM `countries`
	INNER JOIN `static_statistics_countries` ON `countries`.id=`static_statistics_countries`.country_id
WHERE 1=1"
		, 'count_source_type' => 'query'
		, 'order_by' => 'year DESC, total DESC, name_' . Misc::getLanguage()
		, 'anchor_field' => 'id'
		, 'viewfilter' => true
		, 'table_class' => 'admin'
		, 'show_counter' => true
		, 'add_new_button' => array(
				'url' => 'static.country.totals.edit.php?id=0&year=' . addslashes($vf_year) . $urlCheck . '&backurl=[BACKURL]'
				, 'label' => Translations::get('btn_add_new')
			)
		));

	$oView->add_field( new class_field_string ( array(
		'fieldname' => 'name_' . Misc::getLanguage()
		, 'fieldlabel' => Translations::get('fld_country')
		, 'href' => 'static.country.totals.edit.php?id=[FLD:id]' . $urlCheck . '&backurl=[BACKURL]'
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
		'fieldname' => 'year'
		, 'fieldlabel' => Translations::get('fld_year')
		, 'viewfilter' => array(
				'filter' => array (
						array (
							'fieldname' => 'year'
							, 'search_in' => 'year'
							, 'type' => 'string'
							, 'class' => 'quicksearch'
						)
					)
				)
		)));

	$oView->add_field( new class_field_string ( array(
		'fieldname' => 'total'
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