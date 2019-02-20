<?php

class Countries {
	public static function getListOfCountries($including_country=0) {
		global $databases;

		if ( $including_country == '' ) {
			$including_country = 0;
		}

		$arrOfCountries = array();

		$oConn = new class_mysql($databases['default']);
		$oConn->connect();

		$query = 'SELECT * FROM countries WHERE is_deleted=0 OR id=' .$including_country . ' ORDER BY sort_order, name_' . Misc::getLanguage();
		$result = mysql_query($query, $oConn->getConnection());
		if ( mysql_num_rows($result) > 0 ) {

			while ($row = mysql_fetch_assoc($result)) {
				$oCountry = new Country();
				$oCountry->constructFromDatabaseRecord($row);

				$arrOfCountries[] = $oCountry;
			}
			mysql_free_result($result);
		}

		return $arrOfCountries;
	}
}
