<?php

class Registrations {
	public static function getListOfTodaysRegistrations() {
		global $databases;

		$arr = array();

		$oConn = new class_mysql($databases['default']);
		$oConn->connect();

		$query = "SELECT * FROM `visitors` WHERE is_deleted=0 AND revision LIKE '" . date("Y-m-d") . " %' ORDER BY revision DESC ";
		$result = mysql_query($query, $oConn->getConnection());
		if ( mysql_num_rows($result) > 0 ) {

			while ($row = mysql_fetch_assoc($result)) {
//				$o = new Registration();
//				$o->constructFromDatabaseRecord($row);
				$o = $row['firstname'];

				$arr[] = $o;
			}
			mysql_free_result($result);
		}

		return $arr;
	}
}
