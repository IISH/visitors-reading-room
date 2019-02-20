<?php 
require_once "classes/start.inc.php";

//
if ( !isset($settings) ) {
	$settings = array();
}

// first
$content = createLoginPage();
//echo $content;

// then create webpage
$oPage = new Page('design/page.php', $settings);
$oPage->setTitle(Translations::get('website_name'));
$oPage->setContent( $content );

// show page
echo $oPage->getPage();

function createLoginPage() {
	global $protect, $settings, $oWebuser;

	$error = '';

	if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
		$submitType = $protect->requestSubmitType('post', 'submit_type');

		// get values
		$fldLogin = $protect->request('post', 'fldLogin');
		$fldPassword = $protect->request('post', 'fldPassword');

		// quick protect
		$fldLogin = str_replace(array(';', ':', '!', '<', '>', '(', ')', '%'), ' ', $fldLogin);

		// trim
		$fldLogin = trim($fldLogin);
		$fldPassword = trim($fldPassword);

		// use the left part until the space
		$fldLogin = $protect->get_left_part($fldLogin, ' ');

		//
		$_SESSION["bsrs_loginpage"]["loginname"] = $fldLogin;

		$whatToDo = Misc::changeWhatToDoIfSubmitType($submitType);

		if ( $whatToDo == 'switch:language' ) {
			$_SESSION["bsrs_loginpage"]["loginname"] = $fldLogin;
		}

		// check if both field are entered
		if ( $whatToDo == '' ) {
			if ( $fldLogin == '' || $fldPassword == '' ) {
				// if email or password have no value
				$error .= Translations::get('page_login_message_both_fields_required') . "<br>";
			} else {
				$result_login_check = Authentication::authenticate($fldLogin, $fldPassword);

				if ( $result_login_check == 1 ) {
					// retain login name
					$_SESSION["bsrs"]["visitor_loginname"] = $fldLogin;

					// unset login page values
					unset($_SESSION["bsrs_loginpage"]);

					// go to next page
					Misc::goToNextPage('index.php');
				} else {
					// show error
					$error .= Translations::get('page_login_message_login_password_combination_incorrect') . "<br>";
				}
			}
		}
	} else {
		$fldLogin = isset($_SESSION["bsrs_loginpage"]["loginname"]) ? $_SESSION["bsrs_loginpage"]["loginname"] : '';
		if ( isset($_GET["code"]) && $_GET["code"] != '' ) {
			$code = $_GET["code"];
			switch ( $code ) {
				case "notadmin":
					$error .= Translations::get('error_please_login_as_admin') . '<br>';
					break;
				case "notsuperadmin";
					$error .= Translations::get('error_please_login_as_superadmin') . '<br>';
					break;
			}
		}
	}

	// data
	$data = array();
	$labels = array();
	$labels['page_login_lbl_please_log_in'] = Translations::get('page_login_lbl_please_log_in');
	$labels['page_login_lbl_loginname'] = Translations::get('page_login_lbl_loginname');
	$labels['page_login_lbl_password'] = Translations::get('page_login_lbl_password');
	$labels['page_login_loginname_format'] = Translations::get('page_login_loginname_format');
	$labels['page_login_password_comment'] = Translations::get('page_login_password_comment');
	$labels['page_login_btn_clear'] = Translations::get('page_login_btn_clear');
	$labels['page_login_btn_login'] = Translations::get('page_login_btn_login');
	$labels['error'] = $error;
	$data['error_visibility'] = ( $error == '' ) ? 'hidden': '';
	$data['fldLogin'] = $fldLogin;

	// create page/form
	$ret = Misc::fillTemplate(class_file::getFileSource('design/login.php'), $data, $labels);

	return $ret;
}
