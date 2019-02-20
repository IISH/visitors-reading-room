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
$oPage->setTitle(Translations::get('website_name') . ' | ' . Translations::get('admin_page_researchgoals_title'));
$oPage->setContent( $content );
$oPage->setLevel( '../' );
$oPage->setShowMenu( true );

// show page
echo $oPage->getPage();

function createContent() {
	global $protect, $settings, $oWebuser, $databases;

	// title
	$ret = '<h2>' . Translations::get('admin_page_researchgoals_title') . '</h2>';

	require_once("./classes/class_form/class_form.inc.php");
	require_once("./classes/class_form/fieldtypes/class_field_hidden.inc.php");
	require_once("./classes/class_form/fieldtypes/class_field_string.inc.php");
	require_once("./classes/class_form/fieldtypes/class_field_bit.inc.php");
	require_once("./classes/class_form/fieldtypes/class_field_integer.inc.php");
	require_once("./classes/class_form/fieldtypes/class_field_textarea.inc.php");

	$oDb = new class_mysql($databases['default']);
	$oForm = new class_form($settings, $oDb);

	$oForm->set_form( array(
		'query' => 'SELECT * FROM `research_goals` WHERE id=[FLD:id] '
		, 'table' => 'research_goals'
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
		'fieldname' => 'goal_en'
		, 'fieldlabel' => 'Goal (en)'
		, 'required' => 1
		)));

	$oForm->add_field( new class_field_string ( array(
		'fieldname' => 'goal_nl'
		, 'fieldlabel' => 'Goal (nl)'
		, 'required' => 1
		)));

	$oForm->add_field( new class_field_bit ( array(
		'fieldname' => 'has_comment_field'
		, 'fieldlabel' => 'Has comment field'
		)));

	$oForm->add_field( new class_field_integer ( array(
		'fieldname' => 'sort_order'
		, 'fieldlabel' => 'Sort order'
		, 'required' => 1
		, 'onNew' => 999
		)));

	// generate form
	$ret .= $oForm->generate_form();

	return $ret;
}