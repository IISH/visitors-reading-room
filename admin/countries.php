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
$oPage->setTitle(Translations::get('website_name') . ' | ' . Translations::get('admin_page_countries_title'));
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

	// title
	$ret = '<h2>' . Translations::get('admin_page_countries_title') . '</h2>';

	require_once("./classes/class_view/class_view.inc.php");
	require_once("./classes/class_view/fieldtypes/class_field_string.inc.php");

	$oDb = new class_mysql($databases['default']);
	$oView = new class_view($settings, $oDb);

	$oView->set_view( array(
		'query' => "SELECT * FROM `countries` WHERE is_deleted=0 "
		, 'count_source_type' => 'query'
		, 'order_by' => 'sort_order, name_' . Misc::getLanguage()
		, 'anchor_field' => 'id'
		, 'viewfilter' => true
		, 'table_class' => 'admin'
		, 'add_new_button' => array(
				'url' => 'countries.edit.php?id=0' . $urlCheck . '&backurl=[BACKURL]'
				, 'label' => Translations::get('btn_add_new')
			)
		));

	$oView->add_field( new class_field_string ( array(
		'fieldname' => array('name_' . Misc::getLanguage(), 'name_' . Misc::getOtherLanguage())
		, 'fieldlabel' => Translations::get('fld_country')
		, 'if_no_value' => '-no value-'
		, 'view_max_length' => 35
		, 'href' => 'countries.edit.php?id=[FLD:id]' . $urlCheck . '&backurl=[BACKURL]'
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