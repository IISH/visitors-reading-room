<?php
/**
 * Class for loading and getting translations from the database
 */

class Translations {
	private static $is_loaded = false;
	private static $settings = null;
	private static $settings_table = 'translations';

	/**
	 * Load the settings from the database
	 */
	private static function load() {
		global $databases;
		$language = Misc::getLanguage();

		$oConn = new class_mysql($databases['default']);
		$oConn->connect();

		$arr = array();

		// which language are we using (protime has only 2 languages)
		$result = mysql_query('SELECT * FROM ' . self::$settings_table, $oConn->getConnection());
		if ( mysql_num_rows($result) > 0 ) {

			while ($row = mysql_fetch_assoc($result)) {
				$arr[ $row["property"] ] = $row["translation_" . $language];
			}
			mysql_free_result($result);

		}

		self::$settings = $arr;
		self::$is_loaded = true;
	}

	/**
	 * Return the value of the setting
	 *
	 * @param string $setting_name The name of the setting
	 * @return string The value of the setting
	 */
	public static function get($setting_name) {
		if ( !self::$is_loaded ) {
			self::load();
		}

		$value = self::$settings[$setting_name];

		// replace special codes
		$value = str_replace('{year}', date("Y"), $value);

		return $value;
	}

	public function __toString() {
		return "Class: " . get_class($this) . "\n";
	}

	public static function getAllTranslations( $arr ) {
		if ( !self::$is_loaded ) {
			self::load();
		}

		foreach (self::$settings as $key=>$code) {
			$arr[$key] = $code;
		}

		return $arr;
	}
}
