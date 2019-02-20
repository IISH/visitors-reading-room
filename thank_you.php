<?php
require_once "classes/start.inc.php";

//
if ( !isset($settings) ) {
	$settings = array();
}

// first
$content = createThankYouPage();

// then create webpage
$oPage = new Page('design/page.php', $settings);
$oPage->setTitle(Translations::get('website_name'));
$oPage->setContent( $content );

// show page
echo $oPage->getPage();

function createThankYouPage() {
	global $protect;

	if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
		//
		$submitType = $protect->requestSubmitType('post', 'submit_type');

		//
		$whatToDo = Misc::changeWhatToDoIfSubmitType($submitType);
	}

	// data
	$data = array();
	$labels = array();
	$labels['page_thank_you_lbl_thank_you'] = Translations::get('page_thank_you_lbl_thank_you');
	$labels['page_thank_you_lbl_go_to_first_page'] = Translations::get('page_thank_you_lbl_go_to_first_page');
	$labels['page_thank_you_lbl_click_on_checkmark'] = Translations::get('page_thank_you_lbl_click_on_checkmark');

	$data['thank_you_timer_go_to_first_page_in_x_seconds'] = Settings::get('thank_you_timer_go_to_first_page_in_x_seconds');

	// create page/form
	$ret = Misc::fillTemplate(class_file::getFileSource('design/thank_you.php'), $data, $labels);

	return $ret;
}
