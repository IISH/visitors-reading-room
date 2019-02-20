<?php
function debug($object) {
	preprint($object);
}

function preprint( $object ) {
	echo '<pre>';
	print_r( $object );
	echo '</pre>';
}

class Misc {
	public static function get_remote_addr() {
		$retval = '';

		if ( isset($_SERVER["HTTP_X_FORWARDED_FOR"]) && $_SERVER["HTTP_X_FORWARDED_FOR"] != '' ) {
			$retval = trim($_SERVER["HTTP_X_FORWARDED_FOR"]);
		}

		if ( $retval == '' ) {
			$retval = trim($_SERVER["REMOTE_ADDR"]);
		}

		return $retval;
	}

	public static function getDomainNameExclusionsForSqlQuery() {
		$ret = '';
		$separator = '';

		$arr = Settings::get('statistics_emaildomain_exclude_domain_names');
		$arr = str_replace( array(";", ":", ","), ' ', $arr );
		$arr = trim($arr);
		$arr = explode(' ', $arr);

		foreach ( $arr as $item ) {
			$ret .= $separator . "'" . $item . "'";
			$separator = ', ';
		}

		return $ret;
	}

	public static function getResearchGoalFromPost() {
		global $protect;

		$ret = array();

		$arr = Goals::getArrayOfGoals();

		foreach ( $arr as $goal ) {
			$checked = $protect->requestDigits('post', 'fldResearchGoal' . $goal->getId());
			if ( $checked != '' ) {
				$checked = 'yes';
			} else {
				$checked = 'no';
			}

			$text = $protect->request('post', 'fldResearchGoalText' . $goal->getId());

			$ret[] = array( 'id' => $goal->getId(), 'checked' => $checked, 'text' => $text );
		}

		return $ret;
	}

	public static function getFldCheckboxes() {
		global $protect;

		$ret = array();

		$arr = Checkboxes::getListOfCheckboxes();

		foreach ( $arr as $checkbox ) {
			if ( $protect->requestDigits('post', 'fldCheckbox' . $checkbox->getId()) ) {
				$ret[] = $checkbox->getId();
			}
		}

		return $ret;
	}

	public static function createYearSwitcher($current, $min, $max) {
		global $protect;

		$check = $protect->requestDigits('get', 'check');
		$urlCheck = '';
		if ( $check == 1 ) {
			$urlCheck = '&check=' . $check;
		}

		if ( $min > $max ) {
			$tmp = $min;
			$min = $max;
			$max = $tmp;
		}

		$prevNextDate = new PrevNextDate();
		$prevNextDate->dateAsString($current.'-01-01');

		// prev
		if ( $current > $min ) {
			$prev = '<a href="?year=' . $prevNextDate->getPrevYear('%Y') . $urlCheck . '" title="' . Translations::get('popup_title_prev_year') . '">&laquo;</a>';
		} else {
			$prev = '<span class="linkdisabled">&laquo;</span>';
		}

		// next
		if ( $current < $max ) {
			$next = '<a href="?year=' . $prevNextDate->getNextYear('%Y') . $urlCheck . '" title="' . Translations::get('popup_title_next_year') . '">&raquo;</a>';
		} else {
			$next = '<span class="linkdisabled">&raquo;</span>';
		}

		$data = array();
		$labels = array();
		$labels['prev'] = $prev;
		$labels['next'] = $next;
		$labels['label'] = $current;

		$ret = Misc::fillTemplate(class_file::getFileSource('../design/switcher.php'), $data, $labels);

		return $ret;
	}

	public static function createMonthSwitcher($current, $min, $max) {
		if ( $min > $max ) {
			$tmp = $min;
			$min = $max;
			$max = $tmp;
		}

		$prevNextDate = new PrevNextDate();
		$prevNextDate->dateAsString($current.'-01');

		// prev
		if ( $current > $min ) {
			$prev = '<a href="?month=' . $prevNextDate->getPrevMonth('%Y-%m') . '" title="' . Translations::get('popup_title_prev_month') . '">&laquo;</a>';
		} else {
			$prev = '<span class="linkdisabled">&laquo;</span>';
		}

		// set locale
		setlocale(LC_TIME, Misc::getLanguage());

		// current
		$datum = strftime('%B', strtotime($current . '-01'));
		if ( strftime('%Y', strtotime($current . '-01')) != date('Y') ) {
			$datum .= ' ' . strftime('%Y', strtotime($current . '-01'));
		}
		$label = $datum;

		// next
		if ( $current < $max  ) {
			$next = '<a href="?month=' . $prevNextDate->getNextMonth('%Y-%m') . '" title="' . Translations::get('popup_title_next_month') . '">&raquo;</a>';
		} else {
			$next = '<span class="linkdisabled">&raquo;</span>';
		}

		$data = array();
		$labels = array();
		$labels['prev'] = $prev;
		$labels['next'] = $next;
		$labels['label'] = $label;

		$ret = Misc::fillTemplate(class_file::getFileSource('../design/switcher.php'), $data, $labels);

		return $ret;
	}

	public static function createDaySwitcher($current, $min, $max) {
		if ( $min > $max ) {
			$tmp = $min;
			$min = $max;
			$max = $tmp;
		}

		$prevNextDate = new PrevNextDate();
		$prevNextDate->dateAsString($current);

		// prev
		if ( $prevNextDate->getPrevDay('%Y-%m-%d', Settings::get('skip_weekend_days_in_prev_next')) >= $min ) {
			$prev = '<a href="?day=' . $prevNextDate->getPrevDay('%Y-%m-%d', Settings::get('skip_weekend_days_in_prev_next')) . '" title="' . Translations::get('popup_title_prev_workday') . '">&laquo;</a>';
		} else {
			$prev = '<span class="linkdisabled">&laquo;</span>';
		}

		// set locale
		setlocale(LC_TIME, Misc::getLanguage());

		// current
		$weekday = strftime('%A', strtotime($current));
		$day = (strftime('%d', strtotime($current))+0); // hack for windows %e does not work on windows
		$datum = strftime('%B', strtotime($current));
		if ( strftime('%Y', strtotime($current)) != date('Y') ) {
			$datum .= ' ' . strftime('%Y', strtotime($current));
		}
		$label = $weekday . ' ' . $day . ' ' . $datum;

		// next
		if ( $prevNextDate->getNextDay('%Y-%m-%d', Settings::get('skip_weekend_days_in_prev_next')) <= $max ) {
			$next = '<a href="?day=' . $prevNextDate->getNextDay('%Y-%m-%d', Settings::get('skip_weekend_days_in_prev_next')) . '" title="' . Translations::get('popup_title_next_workday') . '">&raquo;</a>';
		} else {
			$next = '<span class="linkdisabled">&raquo;</span>';
		}

		$data = array();
		$labels = array();
		$labels['prev'] = $prev;
		$labels['next'] = $next;
		$labels['label'] = $label;

		$ret = Misc::fillTemplate(class_file::getFileSource('../design/switcher.php'), $data, $labels);

		return $ret;
	}

	public static function ReplaceSpecialFieldsWithDatabaseValues($url, $row) {
		$return_value = $url;

		// vervang in de url, de FLD: door waardes
		$pattern = '/\[FLD\:[a-zA-Z0-9_]*\]/';
		preg_match($pattern, $return_value, $matches);
		while ( count($matches) > 0 ) {
			$return_value = str_replace($matches[0], addslashes($row[str_replace("]", '', str_replace("[FLD:", '', $matches[0]))]), $return_value);
			$matches = null;
			preg_match($pattern, $return_value, $matches);
		}

		$backurl = $_SERVER["QUERY_STRING"];
		if ( $backurl <> "" ) {
			$backurl = "?" . $backurl;
		}
		$backurl = urlencode($_SERVER["SCRIPT_NAME"] . $backurl);
		$return_value = str_replace("[BACKURL]", $backurl, $return_value);

		return $return_value;
	}

	public static function ReplaceSpecialFieldsWithQuerystringValues($url) {
		$return_value = $url;

		// vervang in de url, de FLD: door waardes
		$pattern = '/\[QUERYSTRING\:[a-zA-Z0-9_]*\]/';
		preg_match($pattern, $return_value, $matches);
		while ( count($matches) > 0 ) {
			$return_value = str_replace($matches[0], addslashes($_GET[str_replace("]", '', str_replace("[QUERYSTRING:", '', $matches[0]))]), $return_value);
			$matches = null;
			preg_match($pattern, $return_value, $matches);
		}

		// calculate 'go back' url
		$backurl = $_SERVER["QUERY_STRING"];
		if ( $backurl <> "" ) {
			$backurl = "?" . $backurl;
		}
		$backurl = urlencode($_SERVER["SCRIPT_NAME"] . $backurl);

		// if there is a backurl then place the new blackurl into the string
		$return_value = str_replace("[BACKURL]", $backurl, $return_value);

		return $return_value;
	}

	public static function PlaceURLParametersInQuery($query) {
		$return_value = $query;

		// vervang in de url, de FLD: door waardes
		$pattern = '/\[FLD\:[a-zA-Z0-9_]*\]/';
		preg_match($pattern, $return_value, $matches);
		while ( count($matches) > 0 ) {
			$return_value = str_replace($matches[0], addslashes($_GET[str_replace("]", '', str_replace("[FLD:", '', $matches[0]))]), $return_value);
			$matches = null;
			preg_match($pattern, $return_value, $matches);
		}

		$return_value = str_replace("[BACKURL]", urlencode(Misc::getBackUrl()), $return_value);

		return $return_value;
	}

	public static function getBackUrl() {
		global $protect;

		$ret = '';

		if ( $ret == '' ) {
			if ( isset( $_GET["parentbackurl"] ) ) {
				$ret = $_GET["parentbackurl"];
			}
		}

		if ( $ret == '' ) {
			if ( isset( $_GET["backurl"] ) ) {
				$ret = $_GET["backurl"];
			}
		}

		if ( $ret == '' ) {
			$scriptNameStrippedEdit = str_replace(array('.edit', '_edit'), '', $_SERVER['SCRIPT_NAME']);
			if ( $_SERVER['SCRIPT_NAME'] != $scriptNameStrippedEdit ) {
				$ret = $scriptNameStrippedEdit;
			}
		}

		$ret = str_replace('<', ' ', $ret);
		$ret = str_replace('>', ' ', $ret);

		$ret = trim($ret);

		$ret = $protect->get_left_part($ret);

		return $ret;
	}

	function createGoalsList( $selectedOption, $extraSubdir = '', $extraClass = '' ) {
		$ret = '';

		if ( is_array($selectedOption) ) {
			$tmp = array();
			foreach ( $selectedOption as $sel ) {
				$tmp[] = $sel['id'];
			}
			$selectedOptionAsString = implode(',', $tmp);
		} else {
			$selectedOptionAsString = $selectedOption;
		}

		$arr = Goals::getArrayOfGoals( $selectedOptionAsString );

		$template = class_file::getFileSource($extraSubdir . 'design/field.research_goals.php');

		foreach ( $arr as $goal ) {
			// data
			$data = array();
			$labels = array();
			$data['fieldValue'] = $goal->getId();
			$labels['fieldLabel'] = $goal->getName(Misc::getLanguage());

			$checked = '';
			$fldResearchGoalTextValue = '';

			foreach ( $selectedOption as $option ) {
				if ( $option['id'] == $goal->getId() ) {
					$checked = ( $option['checked'] == 'yes' ) ? 'checked' : '';
					$fldResearchGoalTextValue = $option['text'];
				}
			}

			$labels['fldResearchGoalTextValue'] = $fldResearchGoalTextValue;
			$labels['class'] = $extraClass;
			$data['checked'] = $checked;

			// if goal has no comment field, hide input field
			$typeOfInputField = 'text';
			if ( !$goal->hasCommentField() ) {
				$typeOfInputField = 'hidden';
			}
			$data['typeOfInputField'] = $typeOfInputField;

			//
			$ret .= Misc::fillTemplate($template, $data, $labels);
		}

		return $ret;
	}

	function createCheckboxesList( $selectedOptions, $whatToDo, $extraSubdir = '' ) {
		$ret = '';

		$arr = Checkboxes::getListOfCheckboxes( $selectedOptions );

		$template = class_file::getFileSource($extraSubdir . 'design/field.checkboxes.php');

		foreach ( $arr as $checkbox ) {
			// data
			$data = array();
			$labels = array();
			$data['fieldValue'] = $checkbox->getId();
			$labels['fieldLabel'] = $checkbox->getName(Misc::getLanguage());
			$data['fieldstyle'] = '';

			if ( in_array($checkbox->getId(), $selectedOptions) ) {
				$data['checked'] = 'CHECKED';
			} else {
				$data['checked'] = '';
			}

			if ( $checkbox->isRequired() ) {
				$labels['requiredsign'] = '<a title="' . Translations::get('requiredsign') . '"><sup>*</sup></a>';
				$labels['required'] = 'onclick="checkFieldCheckbox(this);"';

				if ( $whatToDo == 'data_check' && !in_array($checkbox->getId(), $selectedOptions) && $_SERVER['REQUEST_METHOD'] === 'POST' ) {
					$data['fieldstyle'] = 'inputError';
				}
			} else {
				$labels['requiredsign'] = '';
				$labels['required'] = '';
			}

			//
			$ret .= Misc::fillTemplate($template, $data, $labels);
		}

		return $ret;
	}

	public static function createCountryList( $selectedOption ) {
		$ret = '';
		$arr = Countries::getListOfCountries( $selectedOption );

		foreach ( $arr as $country ) {
			$id = $country->getId();
			$label = $country->getName(Misc::getLanguage());

			$selected = '';
			if ( $id == $selectedOption ) {
				$selected = 'SELECTED';
			}
			$ret .= "<OPTION VALUE=\"$id\" $selected >$label</OPTION>\n";
		}

		return $ret;
	}

	public static function setErrorStyle($errorFields, $field) {
		$ret = '';

		if ( in_array($field, $errorFields) ) {
			$ret = 'inputError';
		}

		return $ret;
	}

	public static function getCurrentlyLoggedInUser() {
		return $_SESSION["bsrs"]["visitor_loginname"];
	}

	public static function removeItemFromArray($needle, $haystack) {
		if(($key = array_search($needle, $haystack)) !== false) {
			unset($haystack[$key]);
		}

		return $haystack;
	}

	public function doDataCheck ( $option ) {
		$ret = true;

		switch ( $option ) {
			case "switch:language":
			case "drop:admin":
			case "cancel:registration":
			case "cancel:admin_registration":
				$ret = false;
				break;
			case "password:check":
				$ret = true;
				break;
			default:
				$ret = true;
		}

		return $ret;
	}

	public static function getLanguage() {
		$language = 'en';

		if ( isset( $_SESSION['language'] ) && in_array($_SESSION['language'], explode(";", Settings::get('available_languages')) )  ) {
			$language = $_SESSION['language'];
		}

		$_SESSION['language'] = $language;

		return $language;
	}

	public static function getOtherLanguage( ) {
		$otherLanguage = Misc::getLanguage();

		switch ( $otherLanguage ) {
			case "en":
				$otherLanguage = 'nl';
				break;
			case "nl":
				$otherLanguage = 'en';
				break;
//			default:
//				$otherLanguage = 'en';
		}

		return $otherLanguage;
	}

	public function Generate_Query($arrField, $arrSearch) {
		$retval = '';
		$separatorBetweenValues = '';

		foreach ( $arrSearch as $value ) {
			$separatorBetweenFields = '';
			$retval .= $separatorBetweenValues . " ( ";
			foreach ( $arrField as $field) {
				if ( trim($field) != '' ) {
					$retval .= $separatorBetweenFields . $field . " LIKE '%" . $value . "%' ";
					$separatorBetweenFields = " OR ";
				}
			}
			$retval .= " ) ";
			$separatorBetweenValues = " AND ";
		}

		if ( $retval != '' ) {
			$retval = " AND " . $retval;
		}

		return $retval;
	}

	public function splitStringIntoArray( $text, $pattern = "/[^a-zA-Z0-9]/" ) {
		return preg_split($pattern, $text);
	}

	public function replaceDoubleTripleSpaces( $string ) {
		return preg_replace('!\s+!', ' ', $string);
	}

	public function changeWhatToDoIfSubmitType($submitType, $whatToDoAfter = '') {
		global $oWebuser;

		$whatToDo = '';

		$arrSubmitType = explode(":", $submitType);
		if ( count($arrSubmitType) >= 2 && $arrSubmitType[0] == 'language' && in_array($arrSubmitType[1], explode(";", Settings::get('available_languages')) ) ) {
			$whatToDo = 'switch:language';

			// set language
			$_SESSION["language"] = $arrSubmitType[1];
		} elseif ( $submitType == 'drop:admin'  ) {
			$whatToDo = $submitType;

			// drop admin access";
			$oWebuser->dropAdminAccess();

			// go to first page
			Misc::goToNextPage('index.php');
		} elseif ( $submitType == 'cancel:registration' || $submitType == 'clear:registration' ) {
			$whatToDo = $submitType;

			// unset registration page values
			unset($_SESSION["bsrs_registrationform"]);

			// go to first page
			Misc::goToNextPage('index.php');
		} elseif ( $submitType == 'cancel:admin_registration' ) {
			$backurl = Misc::getBackUrl();

			// go back
			Misc::goToNextPage($backurl);
		} else {
			$whatToDo = $whatToDoAfter;
		}

		return $whatToDo;
	}

	public function goToNextPage( $url, $label = '' ) {
		if ( trim($label) == '' ) {
			$label = Translations::get('next_page');
		}

		//
		Header("Location: " . $url);
		die(Translations::get('go_to') . " <a href=\"" . $url . "\">" . $label . "</a>");
	}

	public static function fillTemplate( $template, $data, $labels = '' ) {
		// Use of undefined constant ENT_HTML401 - assumed 'ENT_HTML401'
		if ( version_compare(phpversion(), '5.4', '<') ) {
			$flags = ENT_COMPAT;
		} else {
			$flags = ENT_COMPAT | ENT_HTML401;
		}

		foreach ( $data as $a => $b ) {
			if ( !is_array($b) ) {
//				$template = str_replace('{' . $a . '}', str_replace("&amp;", "&", htmlentities($b, $flags, "ISO-8859-15", true)), $template);
				$template = str_replace('{' . $a . '}', str_replace("&amp;", "&", $b), $template);
			}
		}

		if ( is_array($labels) ) {
			foreach ( $labels as $a => $b ) {
				$template = str_replace('{' . $a . '}', $b, $template);
			}
		}

		return $template;
	}

	public function fillQuery( $template, $data ) {
		foreach ( $data as $a => $b ) {
			$template = str_replace('{' . $a . '}', addslashes($b), $template);
		}

		return $template;
	}

	public static function getIpAddress() {
		if ( !empty($_SERVER['HTTP_CLIENT_IP']) ) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		return $ip;
	}
}
