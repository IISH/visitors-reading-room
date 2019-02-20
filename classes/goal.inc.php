<?php

class Goal {
	protected $id;
	protected $goal_en;
	protected $goal_nl;
	protected $has_comment_field;
	protected $sort_order;
	protected $is_deleted;

	function __construct() {
	}

	function constructFromDatabaseRecord( $record ) {
		$this->id = $record['id'];
		$this->goal_en = $record['goal_en'];
		$this->goal_nl = $record['goal_nl'];
		$this->has_comment_field = $record['has_comment_field'];
		$this->sort_order = $record['sort_order'];
		$this->is_deleted = $record['is_deleted'];
	}

	public function getId() {
		return $this->id;
	}

	public function getNameEn() {
		return $this->goal_en;
	}

	public function getNameNl() {
		return $this->goal_nl;
	}

	public function getName( $language ) {
		switch( $language ) {
			case "nl":
				$ret = $this->goal_nl;
				break;
			default:
				$ret = $this->goal_en;
		}

		return $ret;
	}

	public function hasCommentField() {
		return $this->has_comment_field;
	}
}
