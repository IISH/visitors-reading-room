<?php

class Goals {
	public static function getArrayOfGoals( $alreadySelected = '' ) {
		global $databases;

		$arr = array();

		if ( is_array($alreadySelected) ) {
			$tmp = array();
			foreach ( $alreadySelected as $sel ) {
				$tmp[] = $sel['id'];
			}
			$alreadySelected = implode(',', $tmp);
		}

		if ( $alreadySelected == '' ) {
			$alreadySelected = 0;
		}

		$oConn = new class_mysql($databases['default']);
		$oConn->connect();

		$query = 'SELECT * FROM research_goals WHERE is_deleted=0 OR id IN (' . $alreadySelected . ') ORDER BY sort_order, goal_' . Misc::getLanguage();

		$result = mysql_query($query, $oConn->getConnection());
		if ( mysql_num_rows($result) > 0 ) {

			while ($row = mysql_fetch_assoc($result)) {
				$oGoal = new Goal();
				$oGoal->constructFromDatabaseRecord($row);

				$arr[] = $oGoal;
			}
			mysql_free_result($result);
		}

		return $arr;
	}

	public static function getSimpleArrayOfGoals( ) {
		global $databases;

		$arr = array();

		$oConn = new class_mysql($databases['default']);
		$oConn->connect();

		$query = 'SELECT * FROM research_goals WHERE is_deleted=0 ORDER BY goal_' . Misc::getLanguage();

		$result = mysql_query($query, $oConn->getConnection());
		if ( mysql_num_rows($result) > 0 ) {

			while ($row = mysql_fetch_assoc($result)) {
				$arr[] = array($row['id'], $row['goal_' . Misc::getLanguage()]);
			}
			mysql_free_result($result);
		}

		return $arr;
	}
}
