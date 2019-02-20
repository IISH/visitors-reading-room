<?php

class Country {
	protected $id;
	protected $tld;
	protected $iso_code;
	protected $name_en;
	protected $name_nl;
	protected $remarks;
	protected $is_deleted;
	protected $sort_order;

	function __construct() {
	}

	function constructFromDatabaseRecord( $record ) {
		$this->id = $record['id'];
		$this->tld = $record['tld'];
		$this->iso_code = $record['iso_code'];
		$this->name_en = $record['name_en'];
		$this->name_nl = $record['name_nl'];
		$this->remarks = $record['remarks'];
	}

	public function getId() {
		return $this->id;
	}

	public function getNameEn() {
		return $this->name_en;
	}

	public function getNameNl() {
		return $this->name_nl;
	}

	public function getName( $language ) {
		switch( $language ) {
			case "nl":
				$ret = $this->name_nl;
				break;
			default:
				$ret = $this->name_en;
		}

		return $ret;
	}
}
