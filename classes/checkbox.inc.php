<?php

class Checkbox {
	protected $id;
	protected $checkbox_en;
	protected $checkbox_nl;
	protected $shortcode_en;
	protected $shortcode_nl;
	protected $default_value;
	protected $is_required;
	protected $sort_order;
	protected $is_deleted;

	function __construct() {
	}

	function constructFromDatabaseRecord( $record ) {
		$this->id = $record['id'];
		$this->checkbox_en = $record['checkbox_en'];
		$this->checkbox_nl = $record['checkbox_nl'];
		$this->shortcode_en = $record['shortcode_en'];
		$this->shortcode_nl = $record['shortcode_nl'];
		$this->default_value = $record['default_value'];
		$this->is_required = $record['is_required'];
		$this->sort_order = $record['sort_order'];
		$this->is_deleted = $record['is_deleted'];
	}

	public function getId() {
		return $this->id;
	}

	public function getNameEn() {
		return $this->checkbox_en;
	}

	public function getNameNl() {
		return $this->checkbox_nl;
	}

	public function getName( $language ) {
		switch( $language ) {
			case "nl":
				$ret = $this->checkbox_nl;
				break;
			default:
				$ret = $this->checkbox_en;
		}

		return $ret;
	}

	public function isRequired() {
		return $this->is_required;
	}

	public function getShortcodeEn() {
		return $this->shortcode_en;
	}

	public function getShortcodeNl() {
		return $this->shortcode_nl;
	}

	public function getShortcode( $language ) {
		switch( $language ) {
			case "nl":
				$ret = $this->shortcode_nl;
				break;
			default:
				$ret = $this->shortcode_en;
		}

		return $ret;
	}
}
