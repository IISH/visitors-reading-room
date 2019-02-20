<?php

class VisitorResearchGoal {
	protected $id;
	protected $revision;
	protected $visitor_id;
	protected $year;
	protected $research_goal_id;
	protected $comment;
	protected $modified_by;
	protected $ip_address;
	protected $is_deleted;

	function __construct() {
		$this->id = 0;
		$this->is_deleted = 0;
	}

	function constructFromDatabaseRecord( $record ) {
		$this->id = $record['id'];
		$this->revision = $record['revision'];
		$this->visitor_id = $record['visitor_id'];
		$this->year = $record['year'];
		$this->research_goal_id = $record['research_goal_id'];
		$this->comment = $record['comment'];
		$this->modified_by = $record['modified_by'];
		$this->ip_address = $record['ip_address'];
		$this->is_deleted = $record['is_deleted'];
	}

	function constructFromFormData( $data ) {
//		$this->id = $data['id'];
		$this->revision = $data['revision'];
		$this->visitor_id = $data['visitor_id'];
		$this->year = $data['year'];
		$this->research_goal_id = $data['research_goal_id'];
		$this->comment = $data['comment'];
		$this->modified_by = $data['modified_by'];
		$this->ip_address = $data['ip_address'];
		$this->is_deleted = $data['is_deleted'];
	}

	function getId() {
		return $this->id;
	}

	function save() {
		global $databases;

		$oConn = new class_mysql($databases['default']);
		$oConn->connect();

		$this->id = VisitorResearchGoals::getVisitorResearchGoalId( $this->research_goal_id, $this->visitor_id, $this->year );

		if ( $this->id == 0 && $this->is_deleted == 0 ) {
			// NEW
			$query = "INSERT INTO visitor_research_goals(date_added, revision, visitor_id, `year`, research_goal_id, comment, modified_by, ip_address, is_deleted) VALUES ('{date_added}', '{revision}', {visitor_id}, {year}, {research_goal_id}, '{comment}', '{modified_by}', '{ip_address}', {is_deleted}) ";
			$query = $this->fillDataInQuery($query);
			$result = mysql_query($query, $oConn->getConnection());
		} elseif ( $this->id > 0 ) {
			// UPDATE
			$query = "UPDATE visitor_research_goals SET revision='{revision}', visitor_id={visitor_id}, `year`={year}, research_goal_id={research_goal_id}, comment='{comment}', modified_by='{modified_by}', ip_address='{ip_address}', is_deleted={is_deleted} WHERE id={id} ";
			$query = $this->fillDataInQuery($query);
			$result = mysql_query($query, $oConn->getConnection());
		}
	}

	private function fillDataInQuery( $query ) {
		$query = str_replace('{id}', $this->id, $query);
		$query = str_replace('{date_added}', date('Y-m-d H:i:s'), $query);
		$query = str_replace('{revision}', $this->revision, $query);
		$query = str_replace('{visitor_id}', $this->visitor_id, $query);
		$query = str_replace('{year}', $this->year, $query);
		$query = str_replace('{research_goal_id}', $this->research_goal_id, $query);
		$query = str_replace('{comment}', addslashes($this->comment), $query);
		$query = str_replace('{modified_by}', addslashes($this->modified_by), $query);
		$query = str_replace('{ip_address}', addslashes($this->ip_address), $query);
		$query = str_replace('{is_deleted}', $this->is_deleted, $query);

		return $query;
	}
}
