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
$oPage->setTitle(Translations::get('website_name') . ' | ' . Translations::get('admin_page_administrators_title'));
$oPage->setContent( $content );
$oPage->setLevel( '../' );
$oPage->setShowMenu( true );

// show page
echo $oPage->getPage();

function createContent() {
	global $protect, $settings, $oWebuser, $databases;

	// title
	$ret = '<h2>' . Translations::get('admin_page_administrators_title') . '</h2>';

	require_once("./classes/class_form/class_form.inc.php");
	require_once("./classes/class_form/fieldtypes/class_field_hidden.inc.php");
	require_once("./classes/class_form/fieldtypes/class_field_string.inc.php");
	require_once("./classes/class_form/fieldtypes/class_field_bit.inc.php");

	$oDb = new class_mysql($databases['default']);
	$oForm = new class_form($settings, $oDb);

	$oForm->set_form( array(
		'query' => 'SELECT * FROM `administrators` WHERE id=[FLD:id] '
		, 'table' => 'administrators'
		, 'primarykey' => 'id'
		, 'disallow_delete' => false
		));

	// required !!!
	$oForm->add_field( new class_field_hidden ( array(
		'fieldname' => 'id'
		, 'fieldlabel' => '#'
		, 'save_field_programmatically_in_db' => true
		)));

	$oForm->add_field( new class_field_string ( array(
		'fieldname' => 'loginname'
		, 'fieldlabel' => Translations::get('fld_loginname')
		, 'required' => 1
		)));

	$oForm->add_field( new class_field_bit ( array(
		'fieldname' => 'is_superadmin'
		, 'fieldlabel' => Translations::get('fld_superadmin') . '?'
		, 'onNew' => '0'
		)));

	// required !!!
	$oForm->add_field( new class_field_hidden ( array(
		'fieldname' => 'is_deleted'
		, 'fieldlabel' => 'is deleted?'
		, 'onNew' => '0'
		)));


	// generate form
	$ret .= $oForm->generate_form();

	return $ret;
}
