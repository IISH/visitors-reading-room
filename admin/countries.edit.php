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

	// title
	$ret = '<h2>' . Translations::get('admin_page_countries_title') . '</h2>';

	require_once("./classes/class_form/class_form.inc.php");
	require_once("./classes/class_form/fieldtypes/class_field_hidden.inc.php");
	require_once("./classes/class_form/fieldtypes/class_field_string.inc.php");
	require_once("./classes/class_form/fieldtypes/class_field_bit.inc.php");
	require_once("./classes/class_form/fieldtypes/class_field_integer.inc.php");
	require_once("./classes/class_form/fieldtypes/class_field_textarea.inc.php");
//	require_once("./classes/class_form/fieldtypes/class_field_javascript.inc.php");

	$oDb = new class_mysql($databases['default']);
	$oForm = new class_form($settings, $oDb);

	$oForm->set_form( array(
		'query' => 'SELECT * FROM `countries` WHERE id=[FLD:id] '
		, 'table' => 'countries'
		, 'primarykey' => 'id'
		, 'disallow_delete' => false
		));

	// required !!!
	$oForm->add_field( new class_field_hidden ( array(
		'fieldname' => 'id'
		, 'fieldlabel' => '#'
		, 'save_field_programmatically_in_db' => true
		)));

	// required !!!
	$oForm->add_field( new class_field_hidden ( array(
		'fieldname' => 'is_deleted'
		, 'fieldlabel' => 'is deleted?'
		, 'onNew' => '0'
		)));

	$oForm->add_field( new class_field_string ( array(
		'fieldname' => 'name_en'
		, 'fieldlabel' => 'Name (en)'
		, 'required' => 1
		)));

	$oForm->add_field( new class_field_string ( array(
		'fieldname' => 'name_nl'
		, 'fieldlabel' => 'Name (nl)'
		, 'required' => 1
		)));

	if ( $check == 1 ) {
		$oForm->add_field( new class_field_string ( array(
			'fieldname' => 'geocoder_id'
			, 'fieldlabel' => 'Code'
			, 'addpostfield' => "
<script language=\"\">
function openGeocoder() {
	url = 'http://data.socialhistory.org/api/geocoder?name=' + document.getElementById('FORM_name_en').value;
	open_page_blank(url);

	return false;
}
</script>
<a href=\"#\" onclick=\"return openGeocoder();\">Search</a>
        "
	)));

		$oForm->add_field( new class_field_string ( array(
			'fieldname' => 'geocoder_year'
			, 'fieldlabel' => 'Year'
			)));

		$oForm->add_field( new class_field_string ( array(
			'fieldname' => 'coordinates'
			, 'fieldlabel' => 'Coordinates'
			, 'addpostfield' => "
<script language=\"\">
function openGoogleMaps() {
	url = 'https://www.google.nl/maps/place/' + document.getElementById('FORM_name_en').value;
	open_page_blank(url);

	return false;
}
</script>
<a href=\"#\" onclick=\"return openGoogleMaps();\">Search</a>
        "
			)));
	}

	$oForm->add_field( new class_field_hidden ( array(
		'fieldname' => 'sort_order'
		, 'fieldlabel' => 'Sort order'
		, 'onNew' => '10'
		)));

	// generate form
	$ret .= $oForm->generate_form();

	return $ret;
}
