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
$oPage->setTitle(Translations::get('website_name') . ' | ' . Translations::get('admin_page_settings_title'));
$oPage->setContent( $content );
$oPage->setLevel( '../' );
$oPage->setShowMenu( true );

// show page
echo $oPage->getPage();

function createContent() {
	global $protect, $settings, $oWebuser, $databases;

	// title
	$ret = '<h2>' . Translations::get('admin_page_settings_title') . '</h2>';

	$id = $protect->requestDigits('get', "id");
	if ( $id == '' ) {
		$id = '0';
	}

	require_once("./classes/class_form/class_form.inc.php");
	require_once("./classes/class_form/fieldtypes/class_field_hidden.inc.php");
	require_once("./classes/class_form/fieldtypes/class_field_string.inc.php");
	require_once("./classes/class_form/fieldtypes/class_field_bit.inc.php");
	require_once("./classes/class_form/fieldtypes/class_field_integer.inc.php");
	require_once("./classes/class_form/fieldtypes/class_field_textarea.inc.php");
	require_once("./classes/class_form/fieldtypes/class_field_readonly.inc.php");

	$oDb = new class_mysql($databases['default']);
	$oForm = new class_form($settings, $oDb);

	$oForm->set_form( array(
		'query' => 'SELECT * FROM `settings` WHERE id=[FLD:id] '
		, 'table' => 'settings'
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

	if ( $id > 0 ) {
		$oForm->add_field( new class_field_readonly ( array(
			'fieldname' => 'property'
			, 'fieldlabel' => 'Code'
			)));
	} else {
		$oForm->add_field( new class_field_string ( array(
			'fieldname' => 'property'
			, 'fieldlabel' => 'Code'
			, 'required' => 1
			)));
	}

	$oForm->add_field( new class_field_textarea ( array(
		'fieldname' => 'value'
		, 'fieldlabel' => 'Value'
		, 'required' => 0
		)));

	$oForm->add_field( new class_field_textarea ( array(
		'fieldname' => 'explanation'
		, 'fieldlabel' => 'Explanation'
		)));

	// generate form
	$ret .= $oForm->generate_form();

	return $ret;
}