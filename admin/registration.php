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
$oPage->setTitle(Translations::get('website_name') . ' | ' . 'Visitor page');
$oPage->setContent( $content );
$oPage->setLevel( '../' );
$oPage->setShowMenu( true );

// show page
echo $oPage->getPage();

function createContent() {
	global $protect, $settings, $oWebuser, $databases;

	// get year
	$year = $protect->requestFieldDefaultMinMax('get', 'year', date("Y"), Settings::get('first_year'), date("Y"));

	// TODO 1 of 4: list of fields
	$data = array();
	$labels = array();
	$errorFields = array();
	$error = '';
	$errorSeparator = '';
	$arrFldCheckboxes = array();
	$currentlyChosenResearchGoals = array();

	$id = $protect->requestDigits('get', "id");
	if ( $id == '' ) {
		$id = '0';
	}

	//
	$data['id'] = $id;
	$data['year'] = $year;

//	$whatToDo = '';
	$data['fldId'] = $id;

	if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
		$submitType = $protect->requestSubmitType('post', 'submit_type');

		//
		$whatToDo = Misc::changeWhatToDoIfSubmitType($submitType, 'data_check');

		// TODO 2 of 4: list of fields

		// GET POST VALUES

		// VISITORS + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + +

		// EMAIL
		$data['fldEmail'] = strtolower($protect->request('post', 'fldEmail'));

		// DONT HAVE EMAIL
		$data['fldDontHaveEmail'] = $protect->requestCheckbox('post', 'fldDontHaveEmail');

		if ( $data['fldDontHaveEmail'] == 'on' ) {
			$data['fldDontHaveEmail'] = 'CHECKED';
		} else {
			// check if email address is empty
			if ( $data['fldEmail'] == '' && Misc::doDataCheck( $whatToDo ) ) {
				$error .= $errorSeparator . Translations::get('page_registrationform_lbl_email') . ' ' . Translations::get('page_registrationform_error_is_required');
				$errorSeparator = '<br>';
				$errorFields[] = 'fldEmail';
				// check if email address is correct
			} elseif ( !filter_var($data['fldEmail'], FILTER_VALIDATE_EMAIL) && Misc::doDataCheck( $whatToDo ) ) {
				$error .= $errorSeparator . Translations::get('page_registrationform_lbl_email') . ' ' . Translations::get('page_registrationform_error_is_incorrect');
				$errorSeparator = '<br>';
				$errorFields[] = 'fldEmail';
			}
		}

		// REMARKS
		$data['fldRemarksIntern'] = strtolower($protect->request('post', 'fldRemarksIntern'));

		// VISITOR_MISC + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + +

		// FIRST NAME
		$data['fldFirstname'] = WordManipulations::fixLowerUpperCase($protect->request('post', 'fldFirstname'));
		if ( $data['fldFirstname'] == '' && Misc::doDataCheck( $whatToDo ) ) {
			$error .= $errorSeparator . Translations::get('page_registrationform_lbl_firstname') . ' ' . Translations::get('page_registrationform_error_is_required');
			$errorSeparator = '<br>';
			$errorFields[] = 'fldFirstname';
		}
//		$_SESSION["bsrs_registrationform"]["fldFirstname"] = $data['fldFirstname'];

		// LAST NAME
		$data['fldLastname'] = WordManipulations::fixLowerUpperCase($protect->request('post', 'fldLastname'));
		if ( $data['fldLastname'] == '' && Misc::doDataCheck( $whatToDo ) ) {
			$error .= $errorSeparator . Translations::get('page_registrationform_lbl_lastname') . ' ' . Translations::get('page_registrationform_error_is_required');
			$errorSeparator = '<br>';
			$errorFields[] = 'fldLastname';
		}
//		$_SESSION["bsrs_registrationform"]["fldLastname"] = $data['fldLastname'];

		//
		$data['fldFirstLastname'] = trim($data['fldFirstname'] . ' ' . $data['fldLastname']);

		// SUBJECT
		$data['fldResearchSubject'] = $protect->request('post', 'fldResearchSubject');

		// NEWSLETTER
		$data['fldNewsletterChecked'] = $protect->requestCheckbox('post', 'fldNewsletter');
		if ( $data['fldNewsletterChecked'] == 'on' ) {
			$data['fldNewsletterChecked'] = 'CHECKED';
		}

		// VISITOR_ADDRESSES + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + +

		// COUNTRY
		$data['fldCountry'] = $protect->requestDigits('post', 'fldCountry');
		if ( $data['fldCountry'] == '' && Misc::doDataCheck( $whatToDo ) ) {
			$error .= $errorSeparator . Translations::get('page_registrationform_lbl_country') . ' ' . Translations::get('page_registrationform_error_is_required');
			$errorSeparator = '<br>';
			$errorFields[] = 'fldCountry';
		}
//		$_SESSION["bsrs_registrationform"]["fldCountry"] = $fldCountry;

		// ADDRESS
		$data['fldAddress'] = $protect->request('post', 'fldAddress');
//		$_SESSION["bsrs_registrationform"]["fldAddress"] = $data['fldAddress'];

		// ADDRESS TMP
		$data['fldAddressTmp'] = $protect->request('post', 'fldAddressTmp');
//		$_SESSION["bsrs_registrationform"]["fldAddressTmp"] = $data['fldAddressTmp'];

		// CITY
		$data['fldCity'] = WordManipulations::fixLowerUpperCase($protect->request('post', 'fldCity'));
//		$_SESSION["bsrs_registrationform"]["fldCity"] = $data['fldCity'];

		// CITY TMP
		$data['fldCityTmp'] = WordManipulations::fixLowerUpperCase($protect->request('post', 'fldCityTmp'));
//		$_SESSION["bsrs_registrationform"]["fldCityTmp"] = $data['fldCityTmp'];

		// COUNTRY TMP
		$data['fldCountryTmp'] = $protect->requestCheckbox('post', 'fldCountryTmp');
		if ( $data['fldCountryTmp'] == 'on' ) {
			$data['fldCountryTmp'] = 'CHECKED';
		}
//		$_SESSION["bsrs_registrationform"]["fldCountryTmp"] = $data['fldCountryTmp'];

		// VISITOR_CHECKBOXES + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + +

		// CHECKBOXES
		$arrFldCheckboxes = Misc::getFldCheckboxes();
		$arrCheckRequiredCheckboxes = Checkboxes::getListOfRequiredCheckboxIds();

		foreach ( $arrFldCheckboxes as $selectedCheckbox ) {
			$arrCheckRequiredCheckboxes = Misc::removeItemFromArray($selectedCheckbox, $arrCheckRequiredCheckboxes);
		}

		// only check and show error messages if button 'save' (data_check) is clicked
//		if ( $whatToDo == 'data_check' ) {
			if ( count($arrCheckRequiredCheckboxes) > 0 ) {
				foreach ($arrCheckRequiredCheckboxes as $notCheckedRequiredCheckbox) {
					$oCheckbox = Checkboxes::getCheckbox( $notCheckedRequiredCheckbox );
					$shortCode = $oCheckbox->getShortcode(Misc::getLanguage());
					$error .= $errorSeparator . $shortCode . ' ' . Translations::get('page_registrationform_error_are_required');
					$errorSeparator = '<br>';
					$errorFields[] = 'fldCheckbox' . $notCheckedRequiredCheckbox;
				}
			}
//		}

		$data['fldCheckbox'] = implode(',', $arrFldCheckboxes);

		// VISITOR_RESEARCH_GOALS + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + +

		$currentlyChosenResearchGoals = Misc::getResearchGoalFromPost();
		$data['fldResearchGoals'] = $currentlyChosenResearchGoals;
		// + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + +

		if ( $error == '' && Misc::doDataCheck( $whatToDo )) {
			//
			Visitors::save2($data, true);

			// go to next page
			Misc::goToNextPage(Misc::getBackUrl());
		}
	} else {
		// GET DB VALUES

		//
		$oVisitor = new Visitor( $id, $year );

		// TODO 3 of 4: list of fields
//		$data['fldId'] = $id;
		$data['fldFirstLastname'] = $oVisitor->getFirstLastname();
		$data['fldEmail'] = $oVisitor->getEmail();
		$data['fldEmail_fieldstyle'] = Misc::setErrorStyle($errorFields, 'fldEmail');
		$fldDontHaveEmail = '';
		if ( $oVisitor->getEmail() == '' ) {
			$fldDontHaveEmail = 'CHECKED';
		}
		$data['fldDontHaveEmail'] = $fldDontHaveEmail;
		$data['fldNewsletterChecked'] = ($oVisitor->getNewsletter() ? 'CHECKED' : '' );
		$data['fldResearchSubject'] = $oVisitor->getSubject();
		$data['fldRemarksIntern'] = $oVisitor->getRemarksIntern();

		$data['fldFirstname'] = $oVisitor->getFirstname();
		$data['fldFirstname_fieldstyle'] = Misc::setErrorStyle($errorFields, 'fldFirstname');
		$data['fldLastname'] = $oVisitor->getLastname();
		$data['fldLastname_fieldstyle'] = Misc::setErrorStyle($errorFields, 'fldLastname');
		$data['fldAddress'] = $oVisitor->getHomelandAddress()->getAddress();
		$data['fldAddressTmp'] = $oVisitor->getTemporaryAddress()->getAddress();
		$data['fldCity'] = $oVisitor->getHomelandAddress()->getCity();
		$data['fldCityTmp'] = $oVisitor->getTemporaryAddress()->getCity();
		$data['fldCountry'] = $oVisitor->getHomelandAddress()->getCountryId();
		$fldCountryTmp = '';
		if ( $oVisitor->getTemporaryAddress()->isFound() ) {
			$fldCountryTmp = 'CHECKED';
		}
		$data['fldCountryTmp'] = $fldCountryTmp;

		//
		$arrFldCheckboxes = $oVisitor->getCheckboxes()->getCheckboxes();

		//
		$currentlyChosenResearchGoals = $oVisitor->getVisitorsResearchGoals();
	}

	// TODO 4 of 4: list of fields
	// load default labels/translations
	$labels = Translations::getAllTranslations($labels);

	// extra labels
	$labels['requiredsign'] = '<a title="' . Translations::get('requiredsign') . '"><sup>*</sup></a>';
	$labels['requiredsign_explanation'] = Translations::get('requiredsign');
	$labels['lblYear'] = $year;

	//
	$data['fldEmail_fieldstyle'] = Misc::setErrorStyle($errorFields, 'fldEmail');
	$data['fldFirstname_fieldstyle'] = Misc::setErrorStyle($errorFields, 'fldFirstname');
	$data['fldLastname_fieldstyle'] = Misc::setErrorStyle($errorFields, 'fldLastname');
	$labels['countryList'] = Misc::createCountryList( $data['fldCountry'] );
	$data['fldCountry_fieldstyle'] = Misc::setErrorStyle($errorFields, 'fldCountry');

	//
	$labels['checkboxesList'] = Misc::createCheckboxesList( $arrFldCheckboxes, '', '../' );

	//
	$labels['goalsList'] = Misc::createGoalsList($currentlyChosenResearchGoals, '../', 'goal_admin' );

	// if there are errors...
	$labels['error'] = $error;
	$labels['error_visibility'] = ( $error == '' ) ? 'hidden': '';

	// create page/form
	$ret = Misc::fillTemplate(class_file::getFileSource('../design/registration_form.admin.php'), $data, $labels);

	return $ret;
}
