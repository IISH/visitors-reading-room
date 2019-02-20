<?php

class WordManipulations {
	/**
	 * If single word (only characters) totally in lower-case or totally in upper case fix lower upper case
	 *
	 * @param $text
	 * @return string
	 */
	public static function fixLowerUpperCase( $text ) {
		$text = trim($text);

		if ($text != '') {

			// check if only lower case characters
			$pattern = "/^[a-z]+$/";
			if ( preg_match($pattern, $text) ) {
				$text[0] = strtoupper($text[0]);
			} else {
				// check if only upper case characters (minimal length 2 characters)
				$pattern = "/^[A-Z]{2,}$/";
				if ( preg_match($pattern, $text) ) {
					$text = strtolower($text);
					$text[0] = strtoupper($text[0]);
				}
			}

		}

		return $text;
	}
}
