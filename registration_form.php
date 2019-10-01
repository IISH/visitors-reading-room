<?php
require_once "classes/start.inc.php";

//
if ( !isset($settings) ) {
	$settings = array();
}

$oWebuser->checkLoggedIn();

// first
$content = createRegistrationPage();

// then create webpage
$oPage = new Page('design/page.php', $settings);
$oPage->setTitle(Translations::get('website_name'));
$oPage->setContent( $content );

// show page
echo $oPage->getPage();

function createRegistrationPage() {
	global $protect, $settings, $oWebuser;

	// data
	$data = array();
	$labels = array();

	// TODO 1 of 4: list of fields
	$whatToDo = '';
	$data['fldCountryId'] = Settings::get('default_country'); // default country
	$error = '';
	$errorSeparator = '';
	$errorFields = array();
	$arrFldCheckboxes = array();
	$fldResearchGoals = array();

	if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
		$submitType = $protect->requestSubmitType('post', 'submit_type');

		if ( $submitType == 'data:load_from_database' ) {
			$data['submit_type'] = '';
			$data['isDirty'] = '1';

			// load data
			$data['fldEmail'] = strtolower($protect->request('post', 'fldEmail'));
			$visitorId = Visitors::getVisitorIdByEmail( $data['fldEmail'] );
			$lastYear = Visitors::lastPreviousYearEmailUsed($data['fldEmail']);

			$oVisitor = new Visitor($visitorId, $lastYear);
//			$data['fldFirstname'] = $oVisitor->getFirstname();
			$data['fldFirstname'] = '';
//			$data['fldLastname'] = $oVisitor->getLastname();
			$data['fldLastname'] = '';
//			$data['fldResearchSubject'] = $oVisitor->getSubject();
			$data['fldResearchSubject'] = '';
//			$data['fldNewsletterChecked'] = $oVisitor->getNewsletter();
			$data['fldNewsletterChecked'] = '';
//			if ( $data['fldNewsletterChecked'] == 'on' || $data['fldNewsletterChecked'] == '1' ) {
//				$data['fldNewsletterChecked'] = 'CHECKED';
//			}
			$data['fldNewsletterChecked'] = '';

			// World address
//			$data['fldAddress'] = $oVisitor->getHomelandAddress()->getAddress();
			$data['fldAddress'] = '';
//			$data['fldCity'] = $oVisitor->getHomelandAddress()->getCity();
			$data['fldCity'] = '';
//			$data['fldCountryId'] = $oVisitor->getHomelandAddress()->getCountryId();
			$data['fldCountryId'] = '';

			// Dutch (Netherlands) address
//			if ( $oVisitor->getTemporaryAddress()->isFound() ) {
//				$data['fldAddressTmp'] = $oVisitor->getTemporaryAddress()->getAddress();
//				$data['fldCityTmp'] = $oVisitor->getTemporaryAddress()->getCity();
//				$data['fldCountryTmp'] = 'CHECKED';
//			} else {
//				$data['fldCountryTmp'] = '';
//			}
			$data['fldAddressTmp'] = '';
			$data['fldCityTmp'] = '';
			$data['fldCountryTmp'] = '';

			// checkboxes research goals
//			$fldResearchGoals = $oVisitor->getVisitorsResearchGoals();
			$fldResearchGoals = array();
		} else {
			//
			$whatToDo = Misc::changeWhatToDoIfSubmitType($submitType, 'data_check');

			// TODO 2 of 4: list of fields

			// VISITORS + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + +

			// EMAIL
			$data['fldEmail'] = strtolower($protect->request('post', 'fldEmail'));
			$_SESSION["bsrs_registrationform"]["fldEmail"] = $data['fldEmail'];

			// DONT HAVE EMAIL
			$data['fldDontHaveEmail'] = $protect->requestCheckbox('post', 'fldDontHaveEmail');

			if ($data['fldDontHaveEmail'] == 'on') {
				$data['fldDontHaveEmail'] = 'CHECKED';
			} else {
				// check if email address is empty
				if ($data['fldEmail'] == '' && Misc::doDataCheck($whatToDo)) {
					$error .= $errorSeparator . Translations::get('page_registrationform_lbl_email') . ' ' . Translations::get('page_registrationform_error_is_required');
					$errorSeparator = '<br>';
					$errorFields[] = 'fldEmail';
					// check if email address is correct
				} elseif (!filter_var($data['fldEmail'], FILTER_VALIDATE_EMAIL) && Misc::doDataCheck($whatToDo)) {
					$error .= $errorSeparator . Translations::get('page_registrationform_lbl_email') . ' ' . Translations::get('page_registrationform_error_is_incorrect');
					$errorSeparator = '<br>';
					$errorFields[] = 'fldEmail';
				}
			}
			$_SESSION["bsrs_registrationform"]["fldDontHaveEmail"] = $data['fldDontHaveEmail'];

			// VISITOR_MISC + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + +

			// FIRST NAME
			$data['fldFirstname'] = WordManipulations::fixLowerUpperCase($protect->request('post', 'fldFirstname'));
			if ($data['fldFirstname'] == '' && Misc::doDataCheck($whatToDo)) {
				$error .= $errorSeparator . Translations::get('page_registrationform_lbl_firstname') . ' ' . Translations::get('page_registrationform_error_is_required');
				$errorSeparator = '<br>';
				$errorFields[] = 'fldFirstname';
			}
			// if single character add dot
			if ( strlen($data['fldFirstname']) == 1) {
				$data['fldFirstname'] .= '.';
			}
			//
			$_SESSION["bsrs_registrationform"]["fldFirstname"] = $data['fldFirstname'];

			// LAST NAME
			$data['fldLastname'] = WordManipulations::fixLowerUpperCase($protect->request('post', 'fldLastname'));
			if ($data['fldLastname'] == '' && Misc::doDataCheck($whatToDo)) {
				$error .= $errorSeparator . Translations::get('page_registrationform_lbl_lastname') . ' ' . Translations::get('page_registrationform_error_is_required');
				$errorSeparator = '<br>';
				$errorFields[] = 'fldLastname';
			}
			// if single character add dot
			if ( strlen($data['fldLastname']) == 1) {
				$data['fldLastname'] .= '.';
			}
			//
			$_SESSION["bsrs_registrationform"]["fldLastname"] = $data['fldLastname'];

			// SUBJECT
			$data['fldResearchSubject'] = $protect->request('post', 'fldResearchSubject');
			$_SESSION["bsrs_registrationform"]["fldResearchSubject"] = $data['fldResearchSubject'];

			// NEWSLETTER
			$data['fldNewsletterChecked'] = $protect->requestCheckbox('post', 'fldNewsletter');
			if ($data['fldNewsletterChecked'] == 'on') {
				$data['fldNewsletterChecked'] = 'CHECKED';
			}
			$_SESSION["bsrs_registrationform"]["fldNewsletter"] = $data['fldNewsletterChecked'];

			// VISITOR_ADDRESSES + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + +

			// COUNTRY
			$data['fldCountryId'] = $protect->requestDigits('post', 'fldCountry');
			if ( $data['fldCountryId'] == '' && Misc::doDataCheck($whatToDo) ) {
				$error .= $errorSeparator . Translations::get('page_registrationform_lbl_country') . ' ' . Translations::get('page_registrationform_error_is_required');
				$errorSeparator = '<br>';
				$errorFields[] = 'fldCountry';
			}
			$_SESSION["bsrs_registrationform"]["fldCountry"] = $data['fldCountryId'];

			// ADDRESS
			$data['fldAddress'] = $protect->request('post', 'fldAddress');
			$_SESSION["bsrs_registrationform"]["fldAddress"] = $data['fldAddress'];

			// ADDRESS TMP
			$data['fldAddressTmp'] = $protect->request('post', 'fldAddressTmp');
			$_SESSION["bsrs_registrationform"]["fldAddressTmp"] = $data['fldAddressTmp'];

			// CITY
			$data['fldCity'] = WordManipulations::fixLowerUpperCase($protect->request('post', 'fldCity'));
			$_SESSION["bsrs_registrationform"]["fldCity"] = $data['fldCity'];

			// CITY TMP
			$data['fldCityTmp'] = WordManipulations::fixLowerUpperCase($protect->request('post', 'fldCityTmp'));
			$_SESSION["bsrs_registrationform"]["fldCityTmp"] = $data['fldCityTmp'];

			// COUNTRY TMP
			$data['fldCountryTmp'] = $protect->requestCheckbox('post', 'fldCountryTmp');
			if ($data['fldCountryTmp'] == 'on') {
				$data['fldCountryTmp'] = 'CHECKED';
			}
			$_SESSION["bsrs_registrationform"]["fldCountryTmp"] = $data['fldCountryTmp'];

			// VISITOR_CHECKBOXES + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + +

			// CHECKBOXES
			$arrFldCheckboxes = Misc::getFldCheckboxes();
			$arrCheckRequiredCheckboxes = Checkboxes::getListOfRequiredCheckboxIds();

			foreach ($arrFldCheckboxes as $selectedCheckbox) {
				$arrCheckRequiredCheckboxes = Misc::removeItemFromArray($selectedCheckbox, $arrCheckRequiredCheckboxes);
			}

			// only check and show error messages if button 'save' (data_check) is clicked
			if ($whatToDo == 'data_check') {
				if (count($arrCheckRequiredCheckboxes) > 0) {
					foreach ($arrCheckRequiredCheckboxes as $notCheckedRequiredCheckbox) {
						$oCheckbox = Checkboxes::getCheckbox($notCheckedRequiredCheckbox);
						$shortCode = $oCheckbox->getShortcode(Misc::getLanguage());
						$error .= $errorSeparator . $shortCode . ' ' . Translations::get('page_registrationform_error_are_required');
						$errorSeparator = '<br>';
						$errorFields[] = 'fldCheckbox' . $notCheckedRequiredCheckbox;
					}
				}
			}

			$_SESSION["bsrs_registrationform"]["fldCheckbox"] = implode(',', $arrFldCheckboxes);

			// VISITOR_RESEARCH_GOALS + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + +

			$fldResearchGoals = Misc::getResearchGoalFromPost();
			saveResearchGoalToSession($fldResearchGoals);

			// + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + +

			if ( $error == '' && Misc::doDataCheck( $whatToDo )) {
				//
				Visitors::save();

				if ( Settings::get('clear_form_immediately_after_save') == '1' ) {
					// unset registration page values
					unset($_SESSION["bsrs_registrationform"]);
				}

				// go to next page
				Misc::goToNextPage('thank_you.php');
			}
		}

	} else {
		// if not POST, try to get the value from the SESSION variables

		// TODO 3 of 4: list of fields
		$data['submit_type'] = '';
		$data['isDirty'] = '0';

		$data['fldEmail'] = isset($_SESSION["bsrs_registrationform"]["fldEmail"]) ? $_SESSION["bsrs_registrationform"]["fldEmail"] : '';
		$data['fldDontHaveEmail'] = isset($_SESSION["bsrs_registrationform"]["fldDontHaveEmail"]) ? $_SESSION["bsrs_registrationform"]["fldDontHaveEmail"] : '';
		$data['fldFirstname'] = isset($_SESSION["bsrs_registrationform"]["fldFirstname"]) ? $_SESSION["bsrs_registrationform"]["fldFirstname"] : '';
		$data['fldLastname'] = isset($_SESSION["bsrs_registrationform"]["fldLastname"]) ? $_SESSION["bsrs_registrationform"]["fldLastname"] : '';
		$data['fldAddress'] = isset($_SESSION["bsrs_registrationform"]["fldAddress"]) ? $_SESSION["bsrs_registrationform"]["fldAddress"] : '';
		$data['fldAddressTmp'] = isset($_SESSION["bsrs_registrationform"]["fldAddressTmp"]) ? $_SESSION["bsrs_registrationform"]["fldAddressTmp"] : '';
		$data['fldCity'] = isset($_SESSION["bsrs_registrationform"]["fldCity"]) ? $_SESSION["bsrs_registrationform"]["fldCity"] : '';
		$data['fldCityTmp'] = isset($_SESSION["bsrs_registrationform"]["fldCityTmp"]) ? $_SESSION["bsrs_registrationform"]["fldCityTmp"] : '';
		$data['fldResearchSubject'] = isset($_SESSION["bsrs_registrationform"]["fldResearchSubject"]) ? $_SESSION["bsrs_registrationform"]["fldResearchSubject"] : '';

		if ( isset($_SESSION["bsrs_registrationform"]["fldCountry"]) && $_SESSION["bsrs_registrationform"]["fldCountry"] != '' ) {
			$data['fldCountryId'] = $_SESSION["bsrs_registrationform"]["fldCountry"];
		}
		$data['fldCountryTmp'] = isset($_SESSION["bsrs_registrationform"]["fldCountryTmp"]) ? $_SESSION["bsrs_registrationform"]["fldCountryTmp"] : '';
		$data['fldNewsletterChecked'] = isset($_SESSION["bsrs_registrationform"]["fldNewsletter"]) ? $_SESSION["bsrs_registrationform"]["fldNewsletter"] : '';
		$arrFldCheckboxes = isset($_SESSION["bsrs_registrationform"]["fldCheckbox"]) ? explode(',', $_SESSION["bsrs_registrationform"]["fldCheckbox"]) : array();

		//
		$fldResearchGoals = getResearchGoalFromSession();
	}

	//
	$data['submit_type'] = '';

	// load default labels/translations
	$labels = Translations::getAllTranslations($labels);

	// extra labels
	$labels['requiredsign'] = '<a title="' . Translations::get('requiredsign') . '"><sup>*</sup></a>';
	$labels['requiredsign_explanation'] = Translations::get('requiredsign');

	// TODO 4 of 4: list of fields
	$data['fldEmail_fieldstyle'] = Misc::setErrorStyle($errorFields, 'fldEmail');
	$data['fldFirstname_fieldstyle'] = Misc::setErrorStyle($errorFields, 'fldFirstname');
	$data['fldLastname_fieldstyle'] = Misc::setErrorStyle($errorFields, 'fldLastname');
	$labels['countryList'] = Misc::createCountryList( $data['fldCountryId'] );
	$data['fldCountry_fieldstyle'] = Misc::setErrorStyle($errorFields, 'fldCountry');
	//
	$labels['checkboxesList'] = Misc::createCheckboxesList( $arrFldCheckboxes, $whatToDo );
	//
	$labels['goalsList'] = Misc::createGoalsList( $fldResearchGoals );

	// if there are errors...
	$labels['error'] = $error;
	$labels['error_visibility'] = ( $error == '' ) ? 'hidden': '';

	// create page/form
	$ret = Misc::fillTemplate(class_file::getFileSource('design/registration_form.php'), $data, $labels);

	return $ret;
}

function saveResearchGoalToSession( $goals ) {
	foreach ( $goals as $goal ) {
		$_SESSION["bsrs_registrationform"]["fldResearchGoal" . $goal['id']] = $goal['checked'];
		$_SESSION["bsrs_registrationform"]["fldResearchGoalText" . $goal['id']] = $goal['text'];
	}
}

function getResearchGoalFromSession() {
	global $protect;

	$ret = array();

	$arr = Goals::getArrayOfGoals();

	foreach ( $arr as $goal ) {
		$checked = '';
		$text = '';

		if ( isset( $_SESSION["bsrs_registrationform"]["fldResearchGoal" . $goal->getId()] ) ) {
			$checked = $_SESSION["bsrs_registrationform"]["fldResearchGoal" . $goal->getId()];
			// check value
			$checked = $protect->requestCheckbox('value', $checked);
		}

		if ( isset( $_SESSION["bsrs_registrationform"]["fldResearchGoalText" . $goal->getId()] ) ) {
			$text = $_SESSION["bsrs_registrationform"]["fldResearchGoalText" . $goal->getId()];
		}

		$ret[] = array( 'id' => $goal->getId(), 'checked' => $checked, 'text' => $text );
	}

	return $ret;
}
