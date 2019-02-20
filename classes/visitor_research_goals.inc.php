<?php

class VisitorResearchGoals {
	public static function getVisitorResearchGoalId( $research_goal_id, $visitor_id, $year ) {
		$ret = 0;

		global $databases;

		$oConn = new class_mysql($databases['default']);
		$oConn->connect();

		$query = "SELECT * FROM visitor_research_goals WHERE visitor_id=$visitor_id AND `year`=$year AND research_goal_id=$research_goal_id ";
		$result = mysql_query($query, $oConn->getConnection());

		if ( mysql_num_rows($result) > 0 ) {
			if ($row = mysql_fetch_assoc($result)) {
				$ret = $row['id'];
			}
			mysql_free_result($result);
		}

		return $ret;
	}
}
