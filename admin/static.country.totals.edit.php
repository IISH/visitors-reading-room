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

	// title
	$ret = '<h2>' . Translations::get('admin_page_statistics_countries_title') . '</h2>';

	$id = $protect->requestDigits('get', "id");
	if ( $id == '' ) {
		$id = '0';
	}

//	$year = $protect->requestDigits('get', "year");
	$year = $protect->request('get', "year");
//	if ( $year == '' ) {
//		$year = '0';
//	}

	require_once("./classes/class_form/class_form.inc.php");
	require_once("./classes/class_form/fieldtypes/class_field_hidden.inc.php");
	require_once("./classes/class_form/fieldtypes/class_field_string.inc.php");
	require_once("./classes/class_form/fieldtypes/class_field_integer.inc.php");
	require_once("./classes/class_form/fieldtypes/class_field_readonly.inc.php");
	require_once("./classes/class_form/fieldtypes/class_field_list.inc.php");

	$oDb = new class_mysql($databases['default']);
	$oForm = new class_form($settings, $oDb);

	$oForm->set_form( array(
		'query' => 'SELECT * FROM `static_statistics_countries` WHERE id=[FLD:id] '
		, 'table' => 'static_statistics_countries'
		, 'primarykey' => 'id'
		, 'disallow_delete' => true
		));

	// required !!!
	$oForm->add_field( new class_field_hidden ( array(
		'fieldname' => 'id'
		, 'fieldlabel' => '#'
		)));

	// required !!!
	$oForm->add_field( new class_field_hidden ( array(
		'fieldname' => 'is_deleted'
		, 'fieldlabel' => 'is deleted?'
		, 'onNew' => '0'
		)));

	$oForm->add_field( new class_field_list ( array(
		'fieldname' => 'country_id'
		, 'fieldlabel' => Translations::get('fld_country')
		, 'required' => 1
		, 'query' => 'SELECT id, name_en FROM countries ORDER BY name_en '
		, 'id_field' => 'id'
		, 'description_field' => 'name_en'
		, 'empty_value' => ''
		, 'show_empty_row' => true
		, 'onNew' => ''
		)));

	$oForm->add_field( new class_field_integer ( array(
		'fieldname' => 'year'
		, 'fieldlabel' => Translations::get('fld_year')
		, 'required' => 1
		, 'onNew' => $year
		, 'min' => Settings::get('first_static_year')
		, 'max' => Settings::get('first_year')-1
		)));

	$oForm->add_field( new class_field_integer ( array(
		'fieldname' => 'total'
		, 'fieldlabel' => Translations::get('fld_total')
		, 'required' => 1
		, 'min' => 1
		)));

	// generate form
	$ret .= $oForm->generate_form();

	return $ret;
}