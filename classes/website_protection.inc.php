<?php

class WebsiteProtection {
	public function getShortUrl() {
		$ret = $_SERVER["QUERY_STRING"];
		if ( $ret != '' ) {
			$ret = "?" . $ret;
		}
		$ret = $_SERVER["SCRIPT_NAME"] . $ret;

		return $ret;
	}

	public function getLongUrl() {
		return 'https://' . ( isset($_SERVER["HTTP_X_FORWARDED_HOST"]) && $_SERVER["HTTP_X_FORWARDED_HOST"] != '' ? $_SERVER["HTTP_X_FORWARDED_HOST"] : $_SERVER["SERVER_NAME"] ) . $this->getShortUrl();
	}

	public function get_remote_addr() {
		$retval = '';

		if ( isset($_SERVER["HTTP_X_FORWARDED_FOR"]) && $_SERVER["HTTP_X_FORWARDED_FOR"] != '' ) {
			$retval = trim($_SERVER["HTTP_X_FORWARDED_FOR"]);
		}

		if ( $retval == '' ) {
			$retval = trim($_SERVER["REMOTE_ADDR"]);
		}

		return $retval;
	}

	public function sendErrorToBrowser($tekst) {
		$val = $tekst;
		$val .= "<br>Please contact the webmaster/IT department.";
		$val .= "<br>We have logged your IP address.";
		$val .= "<br>";

		$val = '<span style="color:red;"><b>' . $val . '</b></span>';

		echo $val;
	}

	public function check_instr_xss($foundxss, $test, $searchvalue) {
		if ( $foundxss == 0 ) {
			if ( strpos($test, $searchvalue) !== false ) {
				$foundxss = 1;
			}
		}
		return $foundxss;
	}

	public function getValue($type = 'get', $field = '') {
		$type = strtolower(trim($type));

		switch ($type) {

			case 'get':

				if ($field == '') {
					$retval = $_GET;
					if ( is_array($retval) ) {
						$retval = implode(';', $retval);
					}
				} else {
					if (isset($_GET[$field])) {
						$retval = $_GET[$field];
					} else {
						$retval = '';
					}
				}

				break;

			case 'post':

				if ($field == '') {
					$retval = $_POST;
					if ( is_array($retval) ) {
						$retval = implode(';', $retval);
					}
				} else {
					if (isset($_POST[$field])) {
						$retval = $_POST[$field];
					} else {
						$retval = '';
					}
				}

				break;

			case 'cookie':

				if ($field == '') {
					$retval = $_COOKIE;
					if ( is_array($retval) ) {
						$retval = implode(';', $retval);
					}
				} else {
					if (isset($_COOKIE[$field])) {
						$retval = $_COOKIE[$field];
					} else {
						$retval = '';
					}
				}

				break;

			case 'session':

				if ($field == '') {
					$retval = $_SESSION;
					if ( is_array($retval) ) {
						$retval = implode(';', $retval);
					}
				} else {
					if (isset($_SESSION[$field])) {
						$retval = $_SESSION[$field];
					} else {
						$retval = '';
					}
				}

				break;

			case 'value':

				$retval = $field;

				break;

			default:
				die('Error 85163274. Unknown type: ' . $type);
		}

		return $retval;
	}

	public function request($type = '', $field = '', $pattern = '') {
		$retval = $this->getValue($type, $field);

		if ($retval != '') {
			if ($pattern != '') {
				if ( preg_match($pattern, $retval) == 0) {
					// niet goed
					$this->sendErrorToBrowser("ERROR 8564125");
					error_log("ERROR 8564125 - command: " . $type . " - value: " . $retval);
					die('');
				}
			}
		}

		return $retval;
	}

	public function requestDigits($type = '', $field = '') {
		$retval = $this->getValue($type, $field);

		$retval = trim($retval);

		if ($retval != '') {
			// check if only digits
			$pattern = "/^[0-9]+$/";
			if ( preg_match($pattern, $retval) == 0) {
				// niet goed
				$this->sendErrorToBrowser("ERROR 5474582");
				error_log("ERROR 5474582 - command: " . $type . " - value: " . $retval);
				die('');
			}
		}

		return $retval;
	}

	public function requestCharactersAndDigits($type = '', $field = '') {
		$retval = $this->getValue($type, $field);

		$retval = trim($retval);

		if ($retval != '') {
			// check if only numbers
			$pattern = "/^[0-9a-zA-Z]+$/";
			if ( preg_match($pattern, $retval) == 0) {
				// niet goed
				$this->sendErrorToBrowser("ERROR 9456725");
				error_log("ERROR 9456725 - command: " . $type . " - value: " . $retval);
				die('');
			}
		}

		return $retval;
	}

	public function requestCheckbox($type = '', $field = '') {
		$retval = $this->getValue($type, $field);

		$retval = trim($retval);

		if ($retval != '') {
			// check if only characters
			$pattern = "/^[a-zA-Z]{0,10}$/";
			if ( preg_match($pattern, $retval) == 0) {
				// niet goed
				$this->sendErrorToBrowser("ERROR 7485795");
				error_log("ERROR 7485795 - command: " . $type . " - value: " . $retval);
				die('');
			}
		}

		return $retval;
	}

	public function requestSubmitType($type = '', $field = '') {
		$retval = $this->getValue($type, $field);

		$retval = trim($retval);

		if ($retval != '') {
			// check if pattern
			$pattern = "/^{?[a-zA-Z0-9_:]+}?$/";
			if ( preg_match($pattern, $retval) == 0) {
				// niet goed
				$this->sendErrorToBrowser("ERROR 5274128");
				error_log("ERROR 5274128 - command: " . $type . " - value: " . $retval);
				die('');
			}
		}

		return $retval;
	}

	public function get_left_part($text, $search = ' ' ) {
		$pos = strpos($text, $search);
		if ( $pos !== false ) {
			$text = substr($text, 0, $pos);
		}

		return $text;
	}

	public function requestFieldDefaultMinMax($type, $field, $default, $min, $max) {
		$year = $this->requestDigits($type, $field);
		if ( $year == '' ) {
			$year = $default;
		}
		if ( $year > $max ) {
			$year = $max;
		}
		if ( $year < $min ) {
			$year = $min;
		}

		return $year;
	}
}
