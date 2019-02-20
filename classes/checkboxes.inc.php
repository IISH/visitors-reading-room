<?php

class Checkboxes {
	public static function getCheckbox( $id ) {
		global $databases;

		$oCheckbox = null;

		$oConn = new class_mysql($databases['default']);
		$oConn->connect();

		$query = 'SELECT * FROM checkboxes WHERE is_deleted=0 AND id = ' . $id;

		$result = mysql_query($query, $oConn->getConnection());
		if ( mysql_num_rows($result) > 0 ) {

			while ($row = mysql_fetch_assoc($result)) {
				$oCheckbox = new Checkbox();
				$oCheckbox->constructFromDatabaseRecord($row);
			}
			mysql_free_result($result);
		}

		return $oCheckbox;
	}

	public static function getListOfCheckboxes( $alreadySelected = '' ) {
		global $databases;

		$arr = array();

		if ( is_array( $alreadySelected ) ) {
			$tmp = array();
			foreach ( $alreadySelected as $key => $code ) {
				$tmp[] = $code;
			}
			$alreadySelected = implode(',', $tmp);
		}

		if ( $alreadySelected == '' ) {
			$alreadySelected = 0;
		}

		$oConn = new class_mysql($databases['default']);
		$oConn->connect();

		$query = 'SELECT * FROM checkboxes WHERE is_deleted=0 OR id IN (' . $alreadySelected . ') ORDER BY sort_order, checkbox_' . Misc::getLanguage();

		$result = mysql_query($query, $oConn->getConnection());
		if ( mysql_num_rows($result) > 0 ) {

			while ($row = mysql_fetch_assoc($result)) {
				$oCheckbox = new Checkbox();
				$oCheckbox->constructFromDatabaseRecord($row);

				$arr[] = $oCheckbox;
			}
			mysql_free_result($result);
		}

		return $arr;
	}

	public static function getListOfRequiredCheckboxIds() {
		global $databases;

		$arr = array();

		$oConn = new class_mysql($databases['default']);
		$oConn->connect();

		$query = 'SELECT * FROM checkboxes WHERE is_deleted=0 AND is_required=1 ORDER BY sort_order, checkbox_' . Misc::getLanguage();

		$result = mysql_query($query, $oConn->getConnection());
		if ( mysql_num_rows($result) > 0 ) {

			while ($row = mysql_fetch_assoc($result)) {
				$arr[] = $row['id'];
			}
			mysql_free_result($result);
		}

		return $arr;
	}
}
