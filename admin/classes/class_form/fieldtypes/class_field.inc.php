<?php
class class_field {
	private $m_fieldname;
	private $m_fieldlabel;
	private $m_required;
	private $m_onNew;
	private $m_addquotes;
	private $m_addpostfield;

	function class_field($fieldsettings) {
		$this->m_fieldname = '';
		$this->m_fieldlabel = '';
		$this->m_required = false;
		$this->m_size = "60";
		$this->m_onNew = '';
		$this->m_addquotes = 1;
		$this->m_class = '';
		$this->m_readonly = 0;
		$this->m_addpostfield = '';

		if ( is_array( $fieldsettings ) ) {
			foreach ( $fieldsettings as $field => $value ) {
				switch ($field) {
					case "fieldname":
						$this->m_fieldname = $fieldsettings["fieldname"];
						break;
					case "fieldlabel":
						$this->m_fieldlabel = $fieldsettings["fieldlabel"];
						break;
					case "required":
						$this->m_required = $fieldsettings["required"];
						break;
					case "size":
						$this->m_size = $fieldsettings["size"];
						break;
					case "onNew":
						$this->m_onNew = $fieldsettings["onNew"];
						break;
					case "addquotes":
						$this->m_addquotes = $fieldsettings["addquotes"];
						break;
					case "class":
						$this->m_class = $fieldsettings["class"];
						break;
					case "readonly":
						$this->m_readonly = $fieldsettings["readonly"];
						break;
					case "addpostfield":
						$this->m_addpostfield = $fieldsettings["addpostfield"];
						break;
				}
			}
		}
	}

	function get_class() {
		return $this->m_class;
	}

	function get_fieldname() {
		return $this->m_fieldname;
	}

	function get_fieldlabel() {
		return $this->m_fieldlabel;
	}

	function get_addpostfield() {
		return $this->m_addpostfield;
	}

	function get_onNew($primary_key = "") {
		$veldwaarde = '';

		if ( $primary_key <> "" ) {
			if ( $_GET[$primary_key] == '' || $_GET[$primary_key] == "0" ) {

				if ( is_array($this->m_onNew) ) {

					switch (trim($this->m_onNew["source"])) {
						case "query_string":
							if ( $this->m_onNew["field"] != "" ) {
								$veldwaarde = ( isset($_GET[trim($this->m_onNew["field"])]) ? $_GET[trim($this->m_onNew["field"])] : '' );
							}
							break;
						case "value":
							if ( $this->m_onNew["value"] != "" ) {
								$veldwaarde = $this->m_onNew["value"];
							}
							break;
					}

				} else {
					$veldwaarde = $this->m_onNew;
				}

			}
		}

		// return the field value (as a string !!!)
		return ($veldwaarde."");
	}

	function get_required_sign() {
		if ( $this->is_field_required() == 1 ) {
			$required = "<font color=\"red\" size=\"-2\" title=\"Required\"><sup>*</sup></font>";
		} else {
			$required = '';
		}

		return $required;
	}

	function is_field_required() {
		return $this->m_required;
	}

	function is_field_value_correct($veldwaarde = "") {
		return 1; // default = okay
	}

	function push_field_into_query_array($query_fields) {
		$value = addslashes($this->get_form_value());

		$value = "'" . $value . "'";

		array_push($query_fields, array($this->get_fieldname() => $value));

		return $query_fields;
	}

	function get_form_value($field = '' ) {
		$retval = '';
		if ( $field == '' ) {
			if ( isset($_POST["FORM_" . $this->get_fieldname()]) ) {
				$retval = $_POST["FORM_" . $this->get_fieldname()];
			}
		} else {
			$retval = $_POST["FORM_" . $field];
		}

		$retval = trim( $retval );

		return $retval;
	}

	function cleanUpLabels($text) {
		$text = str_replace('::REQUIRED::', '', $text);
		$text = str_replace('::CLASS::', '', $text);

		return $text;
	}
}
