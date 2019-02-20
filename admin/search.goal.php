<?php
require_once "../classes/start.inc.php";

//
if ( !isset($settings) ) {
	$settings = array();
}

//
$oWebuser->checkLoggedInAsAdminOrSuperadmin();

// first
$content = createContent();

// then create webpage
$oPage = new Page('../design/page.admin.php', $settings);
$oPage->setTitle(Translations::get('website_name') . ' | ' . Translations::get('admin_page_searchgoal_title'));
$oPage->setContent( $content );
$oPage->setLevel( '../' );
$oPage->setShowMenu( true );

// show page
echo $oPage->getPage();

function createContent() {
	global $protect, $settings, $oWebuser, $databases;

	// get year
	$year = $protect->requestFieldDefaultMinMax('get', 'year', date("Y"), Settings::get('first_year'), date("Y"));

	// title
	$ret = '<h2>' . Translations::get('admin_page_searchgoal_title') . ' ' . Misc::createYearSwitcher($year,Settings::get('first_year'), date("Y")) . '</h2>';

	require_once("./classes/class_view/class_view.inc.php");
	require_once("./classes/class_view/fieldtypes/class_field_string.inc.php");
//	require_once("./classes/class_view/fieldtypes/class_field_date.inc.php");

	$oDb = new class_mysql($databases['default']);
	$oView = new class_view($settings, $oDb);

	$oView->set_view( array(
		'query' => "
SELECT visitors.id, visitor_misc.firstname, visitor_misc.lastname, visitor_research_goals.research_goal_id, goal_en, goal_nl, visitor_research_goals.comment
FROM `visitors`
	INNER JOIN `visitor_misc` ON `visitors`.id=`visitor_misc`.visitor_id
	INNER JOIN `visitor_research_goals` ON `visitors`.id=`visitor_research_goals`.visitor_id
	INNER JOIN `research_goals` ON `visitor_research_goals`.research_goal_id=`research_goals`.id
WHERE `visitors`.is_deleted=0
	AND `visitor_misc`.is_deleted=0
	AND `visitor_research_goals`.is_deleted=0
	AND `visitor_misc`.year=" . $year . "
	AND `visitor_research_goals`.year=" . $year
		, 'count_source_type' => 'query'
		, 'order_by' => 'lastname, firstname, goal_' . Misc::getLanguage()
		, 'anchor_field' => 'id'
		, 'viewfilter' => true
		, 'table_class' => 'admin'
		, 'show_counter' => true
		, 'hidden_fields' => array( array('year' => $year ) )
		));

	$oView->add_field( new class_field_string ( array(
		'fieldname' => 'firstname'
		, 'fieldlabel' => Translations::get('fld_firstname')
		, 'if_no_value' => '-no value-'
		, 'href' => 'registration.php?id=[FLD:id]&year=' . $year . '&backurl=[BACKURL]'
		, 'viewfilter' => array(
			'filter' => array (
				array (
					'fieldname' => 'firstname'
					, 'search_in' => 'firstname'
					, 'type' => 'string'
					, 'class' => 'quicksearch'
					)
				)
			)
		)));

	$oView->add_field( new class_field_string ( array(
		'fieldname' => 'lastname'
		, 'fieldlabel' => Translations::get('fld_lastname')
		, 'if_no_value' => '-no value-'
		, 'href' => 'registration.php?id=[FLD:id]&year=' . $year . '&backurl=[BACKURL]'
		, 'viewfilter' => array(
			'filter' => array (
				array (
					'fieldname' => 'lastname'
					, 'search_in' => 'lastname'
					, 'type' => 'string'
					, 'class' => 'quicksearch'
					)
				)
			)
		)));

	$choices = Goals::getSimpleArrayOfGoals();
	$oView->add_field( new class_field_string ( array(
		'fieldname' => 'goal_' . Misc::getLanguage()
		, 'fieldlabel' => Translations::get('fld_goal')
		, 'view_max_length' => 12
		, 'viewfilter' => array(
			'filter' => array (
				array (
					'fieldname' => 'goal'
					, 'search_in' => 'research_goal_id'
					, 'type' => 'select'
					, 'class' => 'quicksearch'
					, 'choices' => $choices
					)
				)
			)
		)));

	$oView->add_field( new class_field_string ( array(
		'fieldname' => 'comment'
		, 'fieldlabel' => Translations::get('fld_description')
		, 'view_max_length' => 37
		, 'viewfilter' => array(
			'filter' => array (
				array (
					'fieldname' => 'comment'
					, 'search_in' => 'comment'
					, 'type' => 'string'
					, 'class' => 'quicksearch'
					)
				)
			)
		)));

	// generate view
	$ret .= $oView->generate_view();

	return $ret;
}