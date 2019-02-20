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

	require_once("./classes/class_view/class_view.inc.php");
	require_once("./classes/class_view/fieldtypes/class_field_string.inc.php");
	require_once("./classes/class_view/fieldtypes/class_field_integer.inc.php");

	$oDb = new class_mysql($databases['default']);
	$oView = new class_view($settings, $oDb);

	$oView->set_view( array(
		'query' => "SELECT * FROM `checkboxes` WHERE is_deleted=0 "
		, 'count_source_type' => 'query'
		, 'order_by' => 'sort_order, shortcode_' . Misc::getLanguage()
		, 'anchor_field' => 'id'
		, 'viewfilter' => true
		, 'table_class' => 'admin'
		, 'add_new_button' => array(
				'url' => 'checkboxes.edit.php?id=0&backurl=[BACKURL]'
				, 'label' => Translations::get('btn_add_new')
			)
		));

	$oView->add_field( new class_field_string ( array(
		'fieldname' => array('checkbox_' . Misc::getLanguage(), 'checkbox_' . Misc::getOtherLanguage())
		, 'fieldlabel' => Translations::get('long_' . Misc::getLanguage() . '_' . Misc::getOtherLanguage())
		, 'if_no_value' => '-no value-'
		, 'view_max_length' => 60
		, 'href' => 'checkboxes.edit.php?id=[FLD:id]&backurl=[BACKURL]'
		, 'viewfilter' => array(
				'filter' => array (
						array (
							'fieldname' => 'checkbox_' . Misc::getLanguage()
							, 'search_in' => 'checkbox_en;checkbox_nl'
							, 'type' => 'string'
							, 'class' => 'quicksearch'
							)
					)
			)
		)));

	$oView->add_field( new class_field_integer ( array(
		'fieldname' => 'sort_order'
		, 'fieldlabel' => Translations::get('fld_sortorder')
		)));

	// generate view
	$ret .= $oView->generate_view();

	return $ret;
}