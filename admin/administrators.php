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

	require_once("./classes/class_view/class_view.inc.php");
	require_once("./classes/class_view/fieldtypes/class_field_string.inc.php");
	require_once("./classes/class_view/fieldtypes/class_field_bit.inc.php");

	$oDb = new class_mysql($databases['default']);
	$oView = new class_view($settings, $oDb);

	$oView->set_view( array(
		'query' => "SELECT * FROM `administrators` WHERE is_deleted=0 "
		, 'count_source_type' => 'query'
		, 'order_by' => 'loginname'
		, 'anchor_field' => 'id'
		, 'viewfilter' => true
		, 'table_class' => 'admin'
		, 'add_new_button' => array(
				'url' => 'administrators.edit.php?id=0&backurl=[BACKURL]'
				, 'label' => Translations::get('btn_add_new')
			)
		));

	$oView->add_field( new class_field_string ( array(
		'fieldname' => 'loginname'
		, 'fieldlabel' => Translations::get('fld_loginname')
		, 'if_no_value' => '-no value-'
		, 'href' => 'administrators.edit.php?id=[FLD:id]&backurl=[BACKURL]'
		, 'viewfilter' => array(
				'filter' => array (
									array (
										'fieldname' => 'loginname'
										, 'type' => 'string'
										, 'search_in' => 'loginname'
										, 'class' => 'quicksearch'
									)
							)
					)
		)));

	$oView->add_field( new class_field_bit ( array(
		'fieldname' => 'is_superadmin'
		, 'fieldlabel' => Translations::get('fld_superadmin') . '?'
		, 'show_different_values' => true
		, 'different_true_value' => 'yes'
		, 'different_false_value' => 'no'
	)));

	// generate view
	$ret .= $oView->generate_view();

	return $ret;
}