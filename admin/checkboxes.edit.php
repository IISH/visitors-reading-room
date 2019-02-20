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
$oPage->setTitle(Translations::get('website_name') . ' | ' . Translations::get('admin_page_checkboxes_title'));
$oPage->setContent( $content );
$oPage->setLevel( '../' );
$oPage->setShowMenu( true );

// show page
echo $oPage->getPage();

function createContent() {
	global $protect, $settings, $oWebuser, $databases;

	// title
	$ret = '<h2>' . Translations::get('admin_page_checkboxes_title') . '</h2>';

	require_once("./classes/class_form/class_form.inc.php");
	require_once("./classes/class_form/fieldtypes/class_field_hidden.inc.php");
	require_once("./classes/class_form/fieldtypes/class_field_string.inc.php");
	require_once("./classes/class_form/fieldtypes/class_field_bit.inc.php");
	require_once("./classes/class_form/fieldtypes/class_field_integer.inc.php");
	require_once("./classes/class_form/fieldtypes/class_field_textarea.inc.php");

	$oDb = new class_mysql($databases['default']);
	$oForm = new class_form($settings, $oDb);

	$oForm->set_form( array(
		'query' => 'SELECT * FROM `checkboxes` WHERE id=[FLD:id] '
		, 'table' => 'checkboxes'
		, 'primarykey' => 'id'
		, 'disallow_delete' => false
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

	$oForm->add_field( new class_field_string ( array(
		'fieldname' => 'shortcode_en'
		, 'fieldlabel' => 'Short code (en)'
		, 'required' => 1
		)));

	$oForm->add_field( new class_field_textarea ( array(
		'fieldname' => 'checkbox_en'
		, 'fieldlabel' => 'Text (en)'
		, 'required' => 1
		)));

	$oForm->add_field( new class_field_string ( array(
		'fieldname' => 'shortcode_nl'
		, 'fieldlabel' => 'Short code (nl)'
		, 'required' => 1
		)));

	$oForm->add_field( new class_field_textarea ( array(
		'fieldname' => 'checkbox_nl'
		, 'fieldlabel' => 'Text (nl)'
		, 'required' => 1
		)));

	$oForm->add_field( new class_field_bit ( array(
		'fieldname' => 'is_required'
		, 'fieldlabel' => 'Is required?'
		, 'onNew' => '0'
		)));

	$oForm->add_field( new class_field_integer ( array(
		'fieldname' => 'sort_order'
		, 'fieldlabel' => 'Sort order'
		, 'onNew' => '999'
		)));

	// generate form
	$ret .= $oForm->generate_form();

	return $ret;
}