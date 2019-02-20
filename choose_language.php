<?php
require_once "classes/start.inc.php";

//
if ( !isset($settings) ) {
	$settings = array();
}

$oWebuser->checkLoggedIn();

// first
$content = createLanguagePage();

// then create webpage
$oPage = new Page('design/page.php', $settings);
$oPage->setTitle(Translations::get('website_name'));
$oPage->setContent( $content );
$oPage->setShowLanguageChoice(false);

// show page
echo $oPage->getPage();

function createLanguagePage() {
	global $protect, $settings, $oWebuser;

	if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
		$submitType = $protect->requestSubmitType('post', 'submit_type');
		$whatToDo = Misc::changeWhatToDoIfSubmitType($submitType);

		if ( $whatToDo == 'switch:language' ) {
			// go to next page
			Misc::goToNextPage('registration_form.php');
		}
	}

	// data
	$data = array();
	$labels = array();
	$labels['page_choose_language_title'] = Translations::get('page_choose_language_title');
	$labels['you_only_have_to_register_if'] = Translations::get('you_only_have_to_register_if');

	// create list of available languages
	$languages = '';
	foreach ( explode(";", Settings::get('available_languages')) as $language ) {
		$languages .= "<a href=\"#\" onClick=\"return setSubmitTypeAndSubmitForm('language:$language');\"><img class=\"flag\" src=\"images/misc/$language.png\"></a>\n";
	}
	$labels['languages'] = $languages;

	// create page/form
	$ret = Misc::fillTemplate(class_file::getFileSource('design/choose_language.php'), $data, $labels);

	return $ret;
}
